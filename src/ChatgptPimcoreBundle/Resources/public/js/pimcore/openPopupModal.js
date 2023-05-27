function openPopupModal(object){
    this.element = object;
    var languagestore = getLanguages();
   
    var pageForm = new Ext.form.FormPanel({
        border: false,
        defaults: {
            labelWidth: 170,
            anchor: '100%'
        },
        items: [
            {
                xtype: "combo",
                name: "field",
                itemId: "field-" + object.id,
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'name', 'is_localizedfield'],
                    proxy: {
                      type: 'ajax',
                      url: '/admin/chatgpt/object-fields?objectId='+object.id,
                      reader: {
                        type: 'json',
                        rootProperty: 'data'
                      }
                    },
                    autoLoad: true
                }),
                editable: false,
                displayField: 'name',
                valueField: 'id',
                triggerAction: 'all',
                mode: "local",
                fieldLabel: t('Field'),
                listeners: {
                    select: function (combo) {
                        var record = combo.getSelection();
                        var isFieldActive = record.get('is_localizedfield');
                        var name = record.get('name');
                        var languageCombo = combo.up().down('#language-' + object.id);
                        console.log(isFieldActive);
                        if (isFieldActive) {
                            languageCombo.show();
                        } else {
                            languageCombo.hide();
                        }
                        generateDescription(name,'');
                    }
                }
            },
            {
                xtype: "combo",
                name: "language",
                itemId: "language-" + object.id,
                store: languagestore,
                editable: false,
                triggerAction: 'all',
                mode: "local",
                fieldLabel: t('Language'),
                listeners: {
                    select: function (combo) {
                        var field = combo.up().down('#field-' + object.id);
                        var languageCombo = combo.up().down('#language-' + object.id);
                        var record = combo.getSelection();
                        var lang = record.get('id');
                        generateDescription(field.value,languageCombo.getDisplayValue());
                    }.bind(this)
                },
                hidden: true
            },
            {
                xtype: 'textareafield',
                name: 'description',
                fieldLabel: 'Description',
                rows: 4, // Number of rows to display
                enforceMaxLength: false // Enforce the maximum character length
            },{
                xtype: 'numberfield',
                fieldLabel: 'Max Length',
                name: 'max_tokens',
                value: 120
            }
        ]
    });

    var win = new Ext.Window({
        title: "Generate Product Description using ChatGPT",
        width: 600,
        bodyStyle: "padding:10px",
        layout: 'fit',
        height:400,
        items: [pageForm],
        buttons: [{
            text: t("cancel"),
            iconCls: "pimcore_icon_delete",
            handler: function () {
                win.close();
            }
        }, {
            text: t("apply"),
            iconCls: "pimcore_icon_apply",
            handler: function () {
                var params = pageForm.getForm().getFieldValues();
                win.disable();
             
                Ext.Ajax.request({
                    url: "/admin/chatgpt/generate-description",
                    method: 'POST', 
                    params: {
                       
                        objectId: object.id,
                        description: params.description,
                        field:params.field,
                        lang:params.language,
                        max_tokens:params.max_tokens
                    },
                    success: function (response) {
                        var response = Ext.decode(response.responseText);

                        if (response) {

                          
                           
                            if(response.success){
                                object.reload();
                                appCreateWindow("Success", response.message);
                            }else{
                                appCreateWindow("Error!", response.message);
                            }
                            

                        } 
                    win.close();    
                    }.bind(this),

                    failure: function () {
                        win.close();
                      
                    }
                });

            }.bind(this)
        }]
    });

    win.show();





        
}

function getLanguages() {

    var locales = pimcore.settings.websiteLanguages

    var languages = [];

    for (var i = 0; i < locales.length; i++) {
        var langText = pimcore.available_languages[locales[i]] + " [" + locales[i] + "]";
        languages.push([locales[i], langText]);
    }
    ;

    return languages;

}


function generateDescription(field , language=''){
    Ext.Ajax.request({
        url: '/admin/map-description-field?field='+field+'&objectId='+this.element.id+'&language='+language, // Replace with your API endpoint
        method: 'GET',
        success: function(response) {
          var value =JSON.parse(response.responseText);
      
          // Set the value of the textareafield
          var myTextArea = Ext.ComponentQuery.query('[name=description]')[0];
          myTextArea.setValue(value.data);
        },
        failure: function(response) {
          console.error('API call failed');
        }
      });
}

function appCreateWindow(title, text, success = false, objectID = null) {
    var window = new Ext.window.Window({
        minHeight: 150,
        minWidth: 350,
        maxWidth: 700,
        modal: true,
        layout: 'fit',
        bodyStyle: "padding: 10px;",
        title: title,
        html: text,
        buttons: success == false && objectID == null ? "" : [
            {
                text    : 'Reload',
                handler : function () {
                    window.destroy();
                    pimcore.helpers.closeObject(objectID);
                    pimcore.helpers.openObject(objectID);
                }
            }
        ]
    });

    window.show();

    return window;
}