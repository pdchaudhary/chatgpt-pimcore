
pimcore.registerNS("pimcore.plugin.ChatgptPimcoreBundlePlugin");

pimcore.plugin.ChatgptPimcoreBundlePlugin = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.postOpenObject, this.postOpenObject.bind(this));
    },

    postOpenObject: function (e, type) {
     
            var { object, type } = e.detail;
            if(type == "object"){
                var menuParent = object.toolbar;
                var menu =   {
                    xtype: 'button',
                    text: t('ChatGPT'),
                    iconCls: 'pimcore-chat-gpt-icon',
                    
                    listeners: {
                        click: function( menu, item, e, eOpts ) {
                            openPopupModal(object);
                        }
                    }
                
                };
                menuParent.add(menu);
            }
            

      
        
    },
});

var chatgptPimcoreBundlePlugin = new pimcore.plugin.ChatgptPimcoreBundlePlugin();
