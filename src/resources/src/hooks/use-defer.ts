export function tableDefer (data,source) {
    const step = () => {
        requestAnimationFrame(() => {
            const item = source.shift()
            if(item){
                data.push(item)
            }
            if (data.length > 0) {
                step()
            }
        })
    }
    step()
}