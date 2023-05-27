pimcore.registerNS("pimcore.plugin.ChatgptPimcoreBundle");

pimcore.plugin.ChatgptPimcoreBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.ChatgptPimcoreBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("ChatgptPimcoreBundle ready!");
    },

    postOpenObject: function (object, type) {
        /* add quickTranslate icon to objects with localizedfields */

        if (type === "object") {
           


            menuParent = object.toolbar;
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

var ChatgptPimcoreBundlePlugin = new pimcore.plugin.ChatgptPimcoreBundle();

