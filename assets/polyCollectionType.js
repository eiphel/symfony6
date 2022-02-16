(function()
{
    this.PolyCollectionType = function()
	{
        let defaults = {
            fieldPositionName : 'position',
            fieldPrototypeName : '_prototype',
            containerBlockClassName : 'block',
            containerBlocksClassName : 'blocks'
        }

        this.settings = (arguments[1] && typeof arguments[1] === 'object') ? extendDefaults(defaults,arguments[1]) : defaults
        this.container = document.getElementById(arguments[0])
        this.blocks = this.container.getElementsByClassName(this.settings.containerBlocksClassName)[0]
        this.index = 0;

        this.init()
    }

    PolyCollectionType.prototype.init = function()
	{
        let nbItems = this.blocks.children.length
        this.index = nbItems
        let plugin = this

        for(let i = 0; i < nbItems; i++) {
            
            this.blocks.children[i].querySelector('[data-action="delete"]').addEventListener('click', function() {
                let item = this.closest('.' + plugin.settings.containerBlockClassName)
                plugin.remove(item)
            })

            this.blocks.children[i].querySelector('[data-action="up"]').addEventListener('click', function() {
                let item = this.closest('.' + plugin.settings.containerBlockClassName)
                plugin.up(item)
            })

            this.blocks.children[i].querySelector('[data-action="down"]').addEventListener('click', function() {
                let item = this.closest('.' + plugin.settings.containerBlockClassName)
                plugin.down(item)
            })
        }

        this.container.querySelector('[data-action="add"]').addEventListener('click',() => {
            let prototype = decodeEntities(this.container.querySelector('select[name="'+ plugin.settings.fieldPrototypeName +'"]').value);
            let html = prototype.replace(/__name__/g, this.index)
            let content = document.createElement("span")
            content.innerHTML = html
            content = content.firstChild
            content.querySelector('[data-action="delete"]').addEventListener('click', function() {
                 let item = this.closest('.' + plugin.settings.containerBlockClassName)
                 plugin.remove(item)
            })

            content.querySelector('[data-action="up"]').addEventListener('click', function() {
                 let item = this.closest('.' + plugin.settings.containerBlockClassName)
                 plugin.up(item)
            })
            content.querySelector('[data-action="down"]').addEventListener('click', function() {
                 let item = this.closest('.' + plugin.settings.containerBlockClassName)
                 plugin.down(item)
            })
            this.blocks.append(content)
            position(this)
            this.index++
        })
    }

    PolyCollectionType.prototype.remove = function(item)
	{
        item.remove()
        position(this)
	}

    PolyCollectionType.prototype.up = function(item)
	{
        let previousRow = item.previousElementSibling;
        if (previousRow) {
            item.parentNode.insertBefore(item, previousRow);
            position(this)
        }
	}

    PolyCollectionType.prototype.down = function(item)
	{
        let nextRow = item.nextSibling;
        if (nextRow) {
            item.parentNode.insertBefore(nextRow, item);
            position(this)
        }
	}

     function position(plugin) {
        for(let i = 0; i < plugin.blocks.children.length; i++) {
            let input = plugin.blocks.children[i].querySelector('input[name$="'+ plugin.settings.fieldPositionName +']"]')
            if (input) {
                input.value = i
            }
        }
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
})()