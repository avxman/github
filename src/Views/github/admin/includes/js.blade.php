<script>

    const parents = document.querySelectorAll('.left .parent > a')
    const links = document.querySelectorAll('.child .link')
    const url = '{{route('github.web.payload', ['version'=>Config('github.GITHUB_API_VERSION'), 'secret'=>Config('github.GITHUB_TOKEN')] )}}'
    const content = document.getElementById('command')

    function has(item)
    {
        return item !== null && item !== undefined
    }

    // Карусель
    function closeParents(item = Element)
    {
        const _parents = document.querySelectorAll('.left .parent')
        if(has(_parents)){
            _parents.forEach(_item=>{
                if(_item !== item){
                    _item.classList.remove('active')
                }
            })
        }
    }

    function initParents(_parents = NodeList)
    {
        if(has(_parents)){
            _parents.forEach(item=>{
                item.addEventListener('click', e=>{
                    const _item = e.target
                    const _parent = _item.parentNode
                    closeParents(_parent)
                    _parent.classList.toggle('active', !_parent.classList.contains('active'))
                })
            })
        }
    }

    // Открыть команду
    function closeLinks(item = Element)
    {
        const _links = links
        if(has(_links)){
            _links.forEach(_item=>{
                _item.classList.remove('active')
            })
        }
    }

    function addParams(link = '', params = {})
    {
        return link + encodeURI('?' + 'link=' + params.link + '&group=' + params.group + '&view=' + params.view)
    }

    function contentJson(content)
    {
        if(content.status){
            return content.content
        }

        return ''
    }

    function contentHtml(content = '')
    {
        const html = document.createElement('HTML')
        html.insertAdjacentHTML('beforeend', content)
        return html.querySelector('#command').innerHTML
    }

    function theme(res, isJson = true)
    {
        const content = isJson ? contentJson(res) : contentHtml(res)
        const block = document.getElementById('command')
        block.textContent = ''
        block.insertAdjacentHTML('beforeend', content)
    }

    async function send(item = Element)
    {
        let isJson = true
        const _fetch = await fetch(url, {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                link: item.dataset.href,
                group: item.dataset.group,
                view: item.dataset.view,
            })
        })
        const res = await _fetch.text()

        try {
            theme(JSON.parse(res))
        } catch (e) {
            theme(res, false)
        }
    }

    function initLinks(_links = NodeList)
    {
        if(has(_links)) {
            _links.forEach(item => {
                item.addEventListener('click', e=>{
                    const _item = e.target
                    closeLinks(_item)
                    _item.classList.add('active')
                    send(_item)
                })
            })
        }
    }

    // Запуск команды

    function themeCommand(content = '', block = Element)
    {
        const html = document.createElement('HTML')
        html.insertAdjacentHTML('beforeend', content)
        block.textContent = ''
        block.append(html.querySelector('#result'))
    }

    async function ajax(form, block)
    {
        const data = new URLSearchParams(new FormData(form)).toString()
        const _fetch = await fetch(form.dataset.link+'?'+data, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        const res = await _fetch.text()
        themeCommand(res, block)
    }

    function sendCommand(item, form, block)
    {
        if(has(form)){
            form.addEventListener('submit', e=>{

                e.preventDefault()

                const _form = e.target

                ajax(_form, block)

                return false
            })
        }
    }

    function initCommand()
    {
        document.addEventListener('click', e=>{
            const item = e.target
            if(item.classList.contains('btn-send')){
                const form = item.parentNode
                const block = document.querySelector('#command-result')
                sendCommand(item, form, block)
            }
        })
    }

    // Запуск
    function start(_parents = NodeList, _links = NodeList)
    {
        initParents(_parents)
        initLinks(_links)
        initCommand()
    }

    start(parents, links)

</script>
