import app from  './app'
import { defineAsyncComponent } from 'vue'
import render from './components/render.vue'
app.component(render.name,render)
const form = defineAsyncComponent(() =>
    import('./components/form/form.vue')
)
const manyItem = defineAsyncComponent(() =>
    import('./components/form/manyItem.vue')
)
const dialog = defineAsyncComponent(() =>
    import('./components/dialog.vue')
)
const tinymce = defineAsyncComponent(() =>
    import('./components/tinymce.vue')
)
const upload = defineAsyncComponent(() =>
    import('./components/upload.vue')
)
const drawer = defineAsyncComponent(() =>
    import('./components/drawer.vue')
)
const switchs = defineAsyncComponent(() =>
    import('./components/switchs.vue')
)
const tree = defineAsyncComponent(() =>
    import('./components/tree.vue')
)

const grid = defineAsyncComponent(() =>
    import('./components/grid/grid.vue')
)
const icon = defineAsyncComponent(() =>
    import('./components/icon.vue')
)
const batchAction = defineAsyncComponent(() =>
    import('./components/grid/batchAction.vue')
)
import confirm from './components/confirm.vue'

const button = defineAsyncComponent(() =>
    import('./components/button.vue')
)
const DropdownItem = defineAsyncComponent(() =>
    import('./components/dropdown/DropdownItem.vue')
)
const dropdown = defineAsyncComponent(() =>
    import('./components/dropdown/dropdown.vue')
)

const video = defineAsyncComponent(() =>
    import('./components/video.vue')
)

const downloadFile = defineAsyncComponent(() =>
    import('./components/downloadFile.vue')
)
const debugLog = defineAsyncComponent(() =>
    import('./components/debugLog.vue')
)
const echart = defineAsyncComponent(() =>
    import('./components/echart/echart.vue')
)
const echartCard = defineAsyncComponent(() =>
    import('./components/echart/echartCard.vue')
)
const EadminAmap = defineAsyncComponent(() =>
    import('./components/EadminAmap.vue')
)
const filesystem = defineAsyncComponent(() =>
    import('./components/filesystem.vue')
)
const selectTable = defineAsyncComponent(() =>
    import('./components/selectTable.vue')
)
const select = defineAsyncComponent(() =>
    import('./components/select.vue')
)
const EadminTag = defineAsyncComponent(() =>
    import('./components/EadminTag.vue')
)
const spec = defineAsyncComponent(() =>
    import('./components/spec.vue')
)
const display = defineAsyncComponent(() =>
    import('./components/display.vue')
)
const im = defineAsyncComponent(() =>
    import('./components/im/index.vue')
)
const watchComponent = defineAsyncComponent(() =>
    import('./components/watchComponent.vue')
)
const highlight = defineAsyncComponent(() =>
    import('./components/highlight.vue')
)
const EadminCheckboxGroup = defineAsyncComponent(() =>
    import('./components/checkbox.vue')
)
const EadminStep = defineAsyncComponent(() =>
    import('./components/step.vue')
)
const EadminCheckTag = defineAsyncComponent(() =>
    import('./components/EadminCheckTag.vue')
)
const EadminSidebarGrid = defineAsyncComponent(() =>
    import('./components/grid/sidebarGrid.vue')
)
app.component('EadminSidebarGrid',EadminSidebarGrid)
app.component('EadminCheckTag',EadminCheckTag)
app.component('EadminStep',EadminStep)
app.component('EadminCheckboxGroup',EadminCheckboxGroup)
app.component('highlight',highlight)
app.component('EadminForm',form)
app.component('EadminManyItem',manyItem)
app.component('EadminDialog',dialog)
app.component('EadminDrawer',drawer)
app.component('EadminSwitch',switchs)
app.component('EadminGrid',grid)
app.component('EadminTree',tree)
app.component('EadminButton',button)
app.component('EadminConfirm',confirm)
app.component('EadminUpload',upload)
app.component('EadminIcon',icon)
app.component('EadminEditor',tinymce)
app.component('EadminDownloadFile',downloadFile)
app.component('EadminLog',debugLog)
app.component('EadminVideo',video)
app.component('EadminDropdownItem',DropdownItem)
app.component('EadminDropdown',dropdown)
app.component('EadminSelectTable',selectTable)
app.component('EadminChart',echart)
app.component('EadminEchartCard',echartCard)
app.component('BatchAction',batchAction)
app.component('EadminAmap',EadminAmap)
app.component('EadminFileSystem',filesystem)
app.component('EadminSelect',select)
app.component('EadminTag',EadminTag)
app.component('EadminSpec',spec)
app.component('EadminDisplay',display)
app.component('EadminIm',im)
app.component('watchComponent',watchComponent)
