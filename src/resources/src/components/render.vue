<script>
    import {defineComponent, toRaw, h,reactive, resolveComponent,isProxy,resolveDirective,withDirectives,getCurrentInstance,onBeforeUnmount} from 'vue'
    import {splitCode} from '@/utils/splitCode'
    import {setObjectValue,findArrKey} from '@/utils'
    import dayjs from 'dayjs'
    import request from '@/utils/axios'
    export default defineComponent({
        name: "render",
        props: {
            data: {
                type: [String, Number, Array, Object],
                default: '',
            },
            slotProps:Object,
            proxyData:{
                type:Object,
                default:{},
            },
        },
        render() {
            if (this.data) {
                this.setProxyData(this.data,1)
                const jsonRender = toRaw(this.data)
                if (jsonRender.where.AND.length > 0 || jsonRender.where.OR.length > 0) {
                    let expression = this.whereCompile(jsonRender.where.AND, jsonRender.where.OR,this.slotProps)
                    let renderComponent = null
                    expression = expression + ' ? renderComponent = this.renderComponent(jsonRender,this.slotProps) : null'
                    eval(expression)
                    return renderComponent
                }
                return this.renderComponent(jsonRender,this.slotProps)
            } else {
                return null
            }
        },
        setup(props) {
            const modelValue = reactive(props.proxyData)
            onBeforeUnmount(()=>{
                if(props.data){
                    setProxyData(props.data,0)
                }
            })
            const renderComponent = (data, slotProps) => {
                if(!data.attribute){
                    return
                }
                let expression, children = {}, name, attribute
                //属性绑定
                for (let bindAttr in data.bindAttribute) {
                    expression = 'try{data.attribute[bindAttr] = modelValue.' + data.bindAttribute[bindAttr]+'}catch(e){}'
                    eval(expression)
                }
                //双向绑定值
                for (let modelBind in data.modelBind) {
                    let field = data.modelBind[modelBind]
                    // 本次渲染是循环属性
                    if (slotProps && slotProps.row) {
                        expression = 'modelBind == "modelValue" && slotProps.row.'+field+' === null && (data.name === "ElTimePicker" || data.name === "ElDatePicker") ? slotProps.row.' + field + ' = slotProps.row.' + data.bindAttribute.timeValue+':null'
                        eval(expression)
                        expression = 'data.attribute[modelBind] = slotProps.row.' + field
                        eval(expression)
                        data.attribute['onUpdate:'+modelBind] = value => {
                            if(data.attribute.valueFormat){
                                //时间特殊处理
                                if(value == null || value == ''){
                                    slotProps.row[data.bindAttribute.startField] = null
                                    slotProps.row[data.bindAttribute.endField] = null
                                    slotProps.row[data.bindAttribute.timeValue] = null
                                }else{
                                    slotProps.row[data.bindAttribute.timeValue] = dateFormat(value,data.attribute.valueFormat)
                                    if(data.attribute.hasOwnProperty('startField') && data.attribute.hasOwnProperty('endField')){
                                        slotProps.row[data.bindAttribute.startField] = dateFormat(value[0],data.attribute.valueFormat)
                                        slotProps.row[data.bindAttribute.endField] = dateFormat(value[1],data.attribute.valueFormat)
                                    }
                                }
                            }else if(data.attribute.bindFields){
                                //级联选择器处理
                                if(data.name == 'ElCascader' && data.bindAttribute.relation){
                                    slotProps.row[data.bindAttribute.relation] = []
                                    value.forEach(row=>{
                                        var rowValue = {}
                                        data.attribute.bindFields.forEach((field,index)=>{
                                            rowValue[field] = row[index]
                                        })
                                        slotProps.row[data.bindAttribute.relation].push(rowValue)
                                    })
                                }else{
                                    data.attribute.bindFields.forEach((field,index)=>{
                                        slotProps.row[field] = value[index]
                                    })
                                }
                            }

                            slotProps.row[field] = value
                        }
                    } else {
                        expression = 'modelBind == "modelValue" && modelValue.'+field+' === null && (data.name === "ElTimePicker" || data.name === "ElDatePicker") ? modelValue.' + field + ' = modelValue.' + data.bindAttribute.timeValue+':null'
                        eval(expression)
                        expression = 'data.attribute[modelBind] = modelValue.' + field
                        eval(expression)
                        data.attribute['onUpdate:'+modelBind] = value => {
                            if(data.attribute.valueFormat){
                                //时间特殊处理
                                if(value == null){
                                    expression = 'modelValue.' + data.bindAttribute.startField + ' = null'
                                    eval(expression)
                                    expression = 'modelValue.' + data.bindAttribute.endField + ' = null'
                                    eval(expression)
                                    expression = 'modelValue.' + data.bindAttribute.timeValue + ' = null'
                                    eval(expression)
                                }else{
                                    expression = 'modelValue.' + data.bindAttribute.timeValue + ' = dateFormat(value,data.attribute.valueFormat)'
                                    eval(expression)
                                    if(data.attribute.hasOwnProperty('startField') && data.attribute.hasOwnProperty('endField')){
                                        expression = 'modelValue.' + data.bindAttribute.startField + ' = dateFormat(value[0],data.attribute.valueFormat)'
                                        eval(expression)
                                        expression = 'modelValue.' + data.bindAttribute.endField + ' = dateFormat(value[1],data.attribute.valueFormat)'
                                        eval(expression)
                                    }
                                }
                            }else if(data.attribute.bindFields){
                                //级联选择器处理
                                if(data.name == 'ElCascader' && data.bindAttribute.relation){
                                    expression = 'modelValue.' + data.bindAttribute.relation + ' = []'
                                    eval(expression)
                                    value.forEach(row=>{
                                        var rowValue = {}
                                        data.attribute.bindFields.forEach((field,index)=>{
                                            rowValue[field] = row[index]
                                        })
                                        expression = 'modelValue.' + data.bindAttribute.relation + '.push(rowValue)'
                                        eval(expression)
                                    })
                                }else{
                                    data.attribute.bindFields.forEach((field,index)=>{
                                        if(value){
                                            expression = 'modelValue.' + data.bindAttribute[field] + ' = value[index]'
                                        }else{
                                            expression = 'modelValue.' + data.bindAttribute[field] + ' = ""'
                                        }
                                        eval(expression)
                                    })
                                }

                            }
                            expression = 'modelValue.' + field + ' = value'
                            eval(expression)
                        }
                    }
                }
                //事件绑定
                for (let event in data.event) {
                    let eventBind = data.event[event]

                    //弹窗刷新
                    if(event === 'PopupRefresh' && slotProps && slotProps.eadmin_popup){
                        data.attribute.onPopupRefresh = (e)=>{
                            modelValue[slotProps.eadmin_popup] = true
                        }
                    }else if(event === 'GridRefresh' && slotProps && slotProps.grid){
                        //grid刷新事件绑定
                        data.attribute.onGridRefresh = (e)=>{
                            modelValue[slotProps.grid] = true
                        }
                    }else if(event === 'ChangeAjax'){
                        //change改变ajax
                        data.attribute['onChange'] = (value)=>{
                            if(data.attribute.valueFormat){
                              value = dateFormat(value,data.attribute.valueFormat)
                            }
                            if(eventBind.data.field){
                                eventBind.data[eventBind.data.field] = value
                            }
                            request({
                                url:eventBind.url,
                                method: eventBind.method,
                                data:eventBind.data
                            }).then(res=>{
                              if(slotProps && slotProps.grid){
                                setObjectValue(modelValue,eventBind.data.eadmin_editable_bind,0)
                                //刷新editable行数据
                                const index = findArrKey(modelValue[slotProps.grid+'data'],res.data.eadmin_id,'eadmin_id')
                                modelValue[slotProps.grid+'data'][index] = res.data
                              }
                            })
                        }
                    }else{
                        data.attribute['on'+event] = (e)=>{
                            for (let field in eventBind) {
                                if(field == 'gridRefresh'){
                                    setObjectValue(modelValue,slotProps.gridParam,eventBind[field])
                                    modelValue[slotProps.grid] = true
                                }else{
                                    setObjectValue(modelValue,field,eventBind[field])
                                }
                            }
                        }
                    }
                }

                if(!data.attribute.slotProps && slotProps){
                    data.attribute.slotProps = slotProps
                }
                //插槽名称对应内容
                for (let slot in data.content) {
                    children[slot] = (scope) => {
                        if (scope === undefined || !isProxy(scope) && (scope instanceof Object && Object.keys(scope).length == 0)) {
                            scope = slotProps
                        }else{
                            scope = Object.assign(scope,slotProps)
                        }
                        return userRender(data.content[slot], scope)
                    }
                }

                attribute = {...data.attribute}
                if(data.name === 'html'){
                    if(attribute['data-tag'] === 'component'){
                        if(resolveComponent(attribute.key) === attribute.key){
                            getCurrentInstance().appContext.app.component(attribute.key,splitCode(data.content.default[0]))
                        }
                        return h(resolveComponent(attribute.key),attribute)
                    }else{
                        return _createVnode(data.name,attribute['data-tag'] || 'span', attribute, children,data.directive)
                    }
                }
                name = resolveComponent(data.name)
                //for 遍历中的 ElFormItem 验证prop error处理
                if(data.name == 'ElFormItem'){
                    if(slotProps && slotProps.propField){

                        if(!modelValue[slotProps.validator][slotProps.propField][slotProps.$index]){
                            modelValue[slotProps.validator][slotProps.propField][slotProps.$index] = {}
                        }
                        let propField = attribute.prop

                        attribute.prop = slotProps.propField + '.' + slotProps.$index+ '.' + propField

                        attribute.error = modelValue[slotProps.validator][slotProps.propField][slotProps.$index][propField]
                    }
                }
                if (data.map.bindName) {
                    let field = data.map.bindName
                    let mapData = []
                    if (slotProps && slotProps.row) {
                        mapData =  slotProps.row[field] || []
                    }else{
                        try {
                            eval('mapData = modelValue.'+field + ' || []')
                        }catch (e) {

                        }
                    }
                    if(!Array.isArray(mapData)){
                        mapData = []
                    }
                    return mapData.map(item => {
                        for (let attr in data.map.attribute) {
                            attribute[attr] = item[data.map.attribute[attr]]
                        }
                        let mapAttribute = {...attribute}
                        if(mapAttribute.slotDefault){
                            children.default = ()=> mapAttribute.slotDefault
                        }
                        let mapChildren = {...children}
                        return _createVnode(data.name,name, mapAttribute, mapChildren,data.directive)

                    })
                } else {
                    return _createVnode(data.name,name, attribute, children,data.directive)
                }
            }
            function _createVnode(name,component, attribute, children,directives) {

                if(name == 'EadminGrid' || name == 'EadminForm' || name == 'EadminEchartCard' || name == 'watchComponent'){
                    attribute.proxyData = modelValue
                }
                //自定义指令绑定
                let directiveBind = []
                directives.forEach(item=>{
                    directiveBind.push([
                        resolveDirective(item.name),item.value,item.argument
                    ])
                })

                if(directiveBind.length > 0){
                    return withDirectives(h(component, attribute, children),directiveBind)
                }else{

                    return h(component, attribute, children)
                }
            }
            //日期格式格式化 value日期,format格式
            function dateFormat(value,format){
                if(Array.isArray(value)){
                    value = value.map(item=>{
                        if(format){
                            return dayjs(item).format(format)
                        }else{
                            return dayjs(item)
                        }

                    })
                }else{
                    if(format){
                        value = dayjs(value).format(format)
                    }else{
                        value =  dayjs(value)
                    }
                }
                return value
            }
            function userRender(slot, scope) {
                return slot.map(item => {
                    if (typeof (item.where) == 'object' && (item.where.AND.length > 0 || item.where.OR.length > 0)) {
                        // //条件if渲染实现
                        let expression = whereCompile(item.where.AND, item.where.OR,scope)
                        expression = expression + ' ? renderComponent(item,scope) : null'
                        return eval(expression)
                    } else {
                        if (typeof (item) == 'object') {
                            return renderComponent(item, scope)
                        } else {
                            if(item && typeof(item) == 'string' &&item.indexOf('#') !== 0){
                                return h({
                                            setup() {
                                                return {
                                                    ...modelValue
                                                }
                                            },
                                            template: `${item}`,
                                })
                                // if(checkHtml(item) || checkTemplate(item)){
                                //     return h({
                                //         setup() {
                                //             return {
                                //                 ...modelValue
                                //             }
                                //         },
                                //         template: `${item}`,
                                //     })
                                // }else{
                                //     return h({
                                //         setup() {
                                //             return {
                                //                 ...modelValue
                                //             }
                                //         },
                                //         render() {
                                //             return item
                                //         }
                                //     })
                                // }
                            }else{
                                return item
                            }

                        }
                    }
                })
            }
            /**
             * 字符串是否含有html标签的检测
             * @param htmlStr
             */
            function checkHtml(htmlStr) {
                var  reg = /<[^>^\n]+>/g;
                return reg.test(htmlStr);
            }
            /**
             * 字符串是否含有{{}}标签的检测
             * @param htmlStr
             */
            function checkTemplate(htmlStr) {
                var  reg = /\{\{([a-zA-Z]+)\}\}/g;
                return reg.test(htmlStr);
            }
            /**
             * 合并where
             * @param whereAnd
             * @param whereOr
             * @returns {string}
             */
            function whereCompile(whereAnd, whereOr,scope) {
                let expressionStr = parseWhere(whereAnd, 'AND',scope)
                let expressionOr = parseWhere(whereOr, 'OR',scope)
                if (expressionStr && expressionOr) {
                    expressionStr += ' || ' + expressionOr
                } else if (expressionOr) {
                    expressionStr = expressionOr
                }
                return expressionStr
            }

            /**
             * 解析where
             * @param wheres
             * @param op AND | OR
             * @returns {string}
             */
            function parseWhere(wheres, op,scope) {
                let evals = []
                let expression = ''

                wheres.forEach((where, index) => {
                    if (where.where) {
                        let expressionStr = whereCompile(where.where.AND, where.where.OR,scope)
                        evals.push("(" + expressionStr + ")")
                    } else {

                        let  val
                        if(scope && scope.row){
                            val = eval('scope.row.' + where.field)
                        }else{
                            val = eval('modelValue.' + where.field)
                        }
                        if(Array.isArray(val)){
                            if(scope && scope.row){
                                if(where.op == 'notIn'){
                                    evals.push('(scope.row.' + where.field+".indexOf('"+where.condition+"') == -1 && scope.row."+ where.field+".indexOf("+where.condition+") == -1)")
                                }else{
                                    evals.push('(scope.row.' + where.field+".indexOf('"+where.condition+"') >= 0 || scope.row."+ where.field+".indexOf("+where.condition+") >= 0)")
                                }
                            }else{
                                if(where.op == 'notIn'){
                                    evals.push('(modelValue.' + where.field+".indexOf('"+where.condition+"') == -1 && modelValue."+ where.field+".indexOf("+where.condition+") == -1)")
                                }else{
                                    evals.push('(modelValue.' + where.field+".indexOf('"+where.condition+"') >= 0 || modelValue."+ where.field+".indexOf("+where.condition+") >= 0)")
                                }
                            }

                        }else{
                            let operator = where.op
                            if(where.op == 'notIn'){
                                operator = '!='
                            }
                            evals.push("'" + val + "' " + operator + ' ' + "'" + where.condition + "'")
                        }
                    }
                })
                if (op == 'AND') {
                    op = '&&'
                } else {
                    op = '||'
                }
                expression += evals.join(' ' + op + ' ')
                return expression
            }
            //赋值方法
            function setProxyData(data,type){
                for(let field in data.bind){
                    if(!modelValue.hasOwnProperty(field) && type === 1){
                        modelValue[field] = data.bind[field]
                    }
                    if(modelValue.hasOwnProperty(field) && type === 0){
                        delete modelValue[field]
                    }

                }
                for(let slot in data.content){
                    data.content[slot].forEach(item=>{
                        if(typeof(item) == 'object'){
                            setProxyData(item,type)
                        }
                    })
                }
            }
            return {
                setProxyData,
                whereCompile,
                renderComponent
            }
        },
    })
</script>
