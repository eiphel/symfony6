(function()
{
	this.Upload = function()
	{
		let defaults = {
            targetId : 'images_target',
            itemClass : 'item',
            formClass : 'edit',
            buttonsClass : 'buttons',
            serverVarName : 'files',
            urlImg : '/tmp',
            fieldPositionSelectorRule : 'input[name$="position]"]',
            fieldNameSelectorRule : 'input[name$="name]"]',
            fieldDelete : 'del[]',
            beforeLoading : function() { /* this.settings */ },
            afterLoading : function() { },
            onFinish : function() { }
        }

		this.settings = (arguments[1] && typeof arguments[1] === 'object') ? extendDefaults(defaults,arguments[1]) : defaults

        this.target = document.getElementById(this.settings.targetId)
        this.field = document.querySelector('[name="' + arguments[0] + '"]')
        this.remote = this.field.dataset.remote
        this.prototype = this.field.dataset.prototype
        this.isRequered = this.field.required
        this.index = 0;

		this.init()
	}
	
	Upload.prototype.init = function()
	{
        let plugin = this
        let nbItems = this.target.children.length
        this.index = nbItems

        for(let i = 0; i < nbItems; i++) {

            plugin.target.children[i].querySelector('[data-action="delete"]').addEventListener('click', function() {
                let item = this.closest('.' + plugin.settings.itemClass)
                let file = this.dataset.name
                plugin.remove(item, file)
            })

            plugin.target.children[i].querySelector('[data-action="up"]').addEventListener('click', function() {
                let item = this.closest('.' + plugin.settings.itemClass)
                plugin.up(item)
            })

            plugin.target.children[i].querySelector('[data-action="down"]').addEventListener('click', function() {
                let item = this.closest('.' + plugin.settings.itemClass)
                plugin.down(item)
            })

            plugin.target.children[i].querySelector('[data-action="edit"]').addEventListener('click', function() {
                let item = this.closest('.' + plugin.settings.itemClass)
                plugin.showForm(item)
            })

            plugin.target.children[i].querySelector('[data-action="save"]').addEventListener('click', function() {
                let item = this.closest('.' + plugin.settings.itemClass)
                plugin.hideForm(item)
            })
        }

        if (plugin.isRequered && nbItems > 0) {
            plugin.field.removeAttribute('required');
        }

        //plugin.position()

        plugin.field.addEventListener('change', function() {
            plugin.settings.beforeLoading.call(plugin)
            const files = this.files;
            const formData = new FormData()

            for (let i = 0; i < files.length; i++) {
                let file = files[i]
                formData.append(plugin.settings.serverVarName + '[]', file)
            }

            fetch(plugin.remote, {
                method: 'POST',
                headers: new Headers({
                    "X-Requested-With" : "XMLHttpRequest"
                }),
                body: formData,
            }).then((response) => {
                return response.json()
            }).then((json) => {
                plugin.settings.afterLoading.call(plugin)

                for (let i=0; i < json.files.name.length; i++) {
                    let url = plugin.settings.urlImg + '/' + json.files.name[i]
                    let content = decodeEntities(plugin.prototype);
                    content = content.replace(/__name__/g, plugin.index)
                    content = content.replace(/__url__/g, url)  
                    let html = document.createElement("div")                   
                    html.innerHTML = content
                    
                    let input = html.querySelector(plugin.settings.fieldNameSelectorRule)

                    if (input) {
                       input.value = json.files.name[i]    
                    }
                    
                    html = html.querySelector('.' + plugin.settings.itemClass)

                    html.querySelector('[data-action="delete"]').addEventListener('click', function() {
                        let item = this.closest('.' + plugin.settings.itemClass)
                        plugin.remove(item)
                    })

                    html.querySelector('[data-action="up"]').addEventListener('click', function() {
                        let item = this.closest('.' + plugin.settings.itemClass)
                        plugin.up(item)
                    })
        
                    html.querySelector('[data-action="down"]').addEventListener('click', function() {
                        let item = this.closest('.' + plugin.settings.itemClass)
                        plugin.down(item)
                    })

                    html.querySelector('[data-action="edit"]').addEventListener('click', function() {
                        let item = this.closest('.' + plugin.settings.itemClass)
                        plugin.showForm(item)
                    })
        
                    html.querySelector('[data-action="save"]').addEventListener('click', function() {
                        let item = this.closest('.' + plugin.settings.itemClass)
                        plugin.hideForm(item)
                    })
                    plugin.index++ 
                    
                    plugin.target.prepend(html)
                    this.value = ''
                    if (plugin.isRequered) {
                        this.removeAttribute('required');
                    }
                }
                plugin.position()
                plugin.settings.onFinish.call(plugin)
            }).catch((error) => {
                console.log(error.message)
            })
        })
	}
	
	Upload.prototype.remove = function(item, file)
	{
        if(file !== undefined) {
            let input = document.createElement("input")
            input.type = "hidden"
            input.value = file
            input.name = this.settings.fieldDelete
            this.target.parentNode.append(input)
        }
    
        item.remove()

        if (this.isRequered && this.target.children.length == 0) {
            this.field.setAttribute('required', 'required');
        }

        this.position()
	}

    Upload.prototype.up = function(item)
	{
        let previousRow = item.previousElementSibling;
        if (previousRow) {
            this.target.insertBefore(item, previousRow);
            this.position()
        }
	}

    Upload.prototype.down = function(item)
	{
        let nextRow = item.nextSibling;
        if (nextRow) {
            this.target.insertBefore(nextRow, item);
            this.position()
        }
	}
		
	Upload.prototype.position = function()
	{
        for(let i = 0; i < this.target.children.length; i++) {
            let input = this.target.children[i].querySelector(this.settings.fieldPositionSelectorRule)
            if (input) {
                input.value = i
            }
        }
	}

    Upload.prototype.showForm = function(item)
	{
        let buttons = item.getElementsByClassName(this.settings.buttonsClass)[0]
        let edit = item.getElementsByClassName(this.settings.formClass)[0]
        buttons.style.display = 'none'
        edit.style.display = 'block'
	}

    Upload.prototype.hideForm = function(item)
	{
        let buttons = item.getElementsByClassName(this.settings.buttonsClass)[0]
        let edit = item.getElementsByClassName(this.settings.formClass)[0]
        edit.style.display = 'none'
        buttons.style.display = 'block'
	}

    function decodeEntities(encodedString) {
        var translate_re = /&(nbsp|amp|quot|lt|gt);/g;
        var translate = {
            "nbsp":" ",
            "amp" : "&",
            "quot": "\"",
            "lt"  : "<",
            "gt"  : ">"
        };
        return encodedString.replace(translate_re, function(match, entity) {
            return translate[entity];
        }).replace(/&#(\d+);/gi, function(match, numStr) {
            var num = parseInt(numStr, 10);
            return String.fromCharCode(num);
        });
    }

	function extendDefaults(defaults,properties)
	{
		Object.keys(properties).forEach(property => {
			if(properties.hasOwnProperty(property))
			{
				defaults[property] = properties[property]
			}
		});
		return defaults
	}
}())