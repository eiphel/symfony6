(function()
{
    this.CollectionType = function()
    {
        let defaults = {
            buttonAddLabel : 'Add',
            buttonDeleteLabel : 'Delete',
            buttonUpLabel : 'Up',
            buttonDownLabel : 'Down',
            containerAddButtonClassName : 'addButton',
            containerItemClassName : 'item',
            containerButtonsClassName : 'buttons',
            managePosition : false,
            fieldPositionName : 'position'
        }

        this.settings = (arguments[1] && typeof arguments[1] === 'object') ? extendDefaults(defaults,arguments[1]) : defaults
        this.container = document.getElementById(arguments[0])
        this.prototype = decodeEntities(this.container.dataset.prototype)
        this.index = 0;
        this.init()
    }

    CollectionType.prototype.init = function()
	{   
        let plugin = this
        let nbItems = plugin.container.children.length
        this.index = nbItems

        for(let i = 0; i < nbItems; i++) {
            plugin.container.children[i].classList.add(plugin.settings.containerItemClassName)
            plugin.container.children[i].append(buttons(plugin))
        }

    
        let div = document.createElement('div')
        div.classList.add(plugin.settings.containerAddButtonClassName )
        let buttonAdd = createButton(plugin.settings.buttonAddLabel)
        div.append(buttonAdd)
        plugin.container.after(div);

        buttonAdd.addEventListener('click', function(){
            let html = plugin.prototype.replace(/__name__/g, plugin.index)
            let content = document.createElement("span")
            content.innerHTML = html
            content = content.firstChild
            content.classList.add(plugin.settings.containerItemClassName)
            content.append(buttons(plugin))
            plugin.container.append(content)
            plugin.index++
        });
    }

	CollectionType.prototype.remove = function(item)
	{
        item.remove()
        position(this)
	}

    CollectionType.prototype.up = function(item)
	{
        let previousRow = item.previousElementSibling;
        if (previousRow) {
            item.parentNode.insertBefore(item, previousRow);
            position(this)
        }
	}

    CollectionType.prototype.down = function(item)
	{
        let nextRow = item.nextSibling;
        if (nextRow) {
            item.parentNode.insertBefore(nextRow, item);
            position(this)
        }
	}

    function buttons(plugin) {
        let div = document.createElement('div')
        div.classList.add(plugin.settings.containerButtonsClassName)
        let buttonDelete = createButton(plugin.settings.buttonDeleteLabel)
        div.append(buttonDelete)

        buttonDelete.addEventListener('click', function(){
            let item = this.closest('.' + plugin.settings.containerItemClassName)
            plugin.remove(item)
        });

        if (plugin.settings.managePosition) {
            let buttonUp = createButton(plugin.settings.buttonUpLabel)
            div.append(buttonUp)
            let buttonDown = createButton(plugin.settings.buttonDownLabel)
            div.append(buttonDown)

            buttonUp.addEventListener('click', function(){
                let item = this.closest('.' + plugin.settings.containerItemClassName)
                plugin.up(item)
            });

            buttonDown.addEventListener('click', function(){
                let item = this.closest('.' + plugin.settings.containerItemClassName)
                plugin.down(item)
            });
        }
        return div;
    }

    function createButton(label) {
        let button = document.createElement('button');
        button.type = 'button'
        button.innerText = label
        return button
    }

    function position(plugin) {
        for(let i = 0; i < plugin.container.children.length; i++) {
            let input = plugin.container.children[i].querySelector('input[name$="'+ plugin.settings.fieldPositionName +']"]')
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
}())