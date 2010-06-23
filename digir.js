Ext.onReady(function(){
    var searchSet  = new Ext.form.FieldSet({
            title: 'Search',
            width: 640,
            labelWidth: 1,
            layout: "column",
            style: {
                marginLeft: "10px"
            },
            defaults: {
                style: {
                    marginBottom: "5px",
                    marginLeft: "5px"
                }
            },
            items:[ 
                new Ext.form.ComboBox({
                    mode: "local",
                    id: 'url',
                    emptyText: "URL",
                    width: 450,
                    store: new Ext.data.ArrayStore({
                        fields: ['url'],
                        data: digirUrls.map(function(i) {return [i]})
                    }),
                    valueField: "url",
                    displayField: "url"
                }),
                new Ext.form.TextField({emptyText:"Resource name",name:'resource',id:'resource', width:150}),
                new Ext.form.ComboBox({
                    mode: "local",
                    id: 'field',
                    width: 120,
                    forceSelection: true,
                    store: new Ext.data.ArrayStore({
                        fields: ['field'],
                        data: darwinFields.map(function(i){return [i]}) 
                    }),
                    value: "ScientificName",
                    valueField: "field",
                    displayField: "field"
                }),
                new Ext.form.ComboBox({
                    mode: "local",
                    id: 'operation',
                    width: 80,
                    store: new Ext.data.ArrayStore({
                        fields: ['operation'],
                        data: [["like"],["equals"]]
                    }),
                    value:"like",
                    forceSelection: true,
                    valueField: "operation",
                    displayField: "operation"
                }),
                new Ext.form.TextField({name:'term',id:'term',width:250, emptyText: ""})
            ],
            buttons: [
                {text:"List resources",
                 handler: function() {
                    var url = searchSet.findById('url').getValue();
                    var query = "SELECT * FROM '"+url+"'" ;
                    resourceStore.removeAll();
                    resourceStore.setBaseParam('query',query);
                    resourceStore.load();
                }},
                {text:"Search",
                 handler: function() {
                    var url = searchSet.findById('url').getValue();
                    var resource = searchSet.findById('resource').getValue();
                    var field = searchSet.findById('field').getValue();
                    var operation = searchSet.findById('operation').getValue();
                    if(operation == "equals") operation = "=";
                    var term = searchSet.findById('term').getValue();
                    if(operation == "like") {
                        term = '%'+term+'%';
                    }
                    var query = "SELECT * FROM '"+url+"'.'"+ resource+ "' WHERE "+field+" "+operation+" '"+term+"'";
                    searchStore.removeAll();
                    searchStore.setBaseParam('query',query);
                    searchStore.load({callback: function(r,o,s) {
                                            searchResult.setTitle("Search Result: "+ searchStore.getCount())
                                        }});
                }}
            ]
        });

    var searchForm = new Ext.form.FormPanel({
            title: 'DIGIR',
            width: 665,
            style: {
                marginTop: "10px",
                marginLeft: "10px",
                marginRight: "10px"
            },
            items: [ searchSet ]
        });

    searchForm.render('search-panel');

    var resourceStore = new Ext.data.Store({
                id: "resourceStore",
                proxy: new Ext.data.HttpProxy({
                        url: "service.php",
                        method: 'POST'
                    }),
                reader: new Ext.data.JsonReader({
                        root:"result",
                        idProperty: "code",
                        totalProperty:"count",
                        fields: [{name:"code"},{name:"name"}]
                    })
            });

    var resourceResult = new Ext.grid.GridPanel({
            colModel: new Ext.grid.ColumnModel([
                    {readOnly: true, dataIndex: 'name', width: 190,header: "Name"},
                    {readOnly: true, dataIndex: 'code', width: 90, header: "Code"}
                ]),
            width: 300,
            height: 165,
            autoScroll: true,
            store: resourceStore,
            style: {
                marginTop: "10px",
                marginLeft: "10px",
                marginRight: "10px"
            },
            sm: new Ext.grid.RowSelectionModel({singleSelect: true}),
            title: 'Resource Result'
        });

    var resourceLoading = new Ext.LoadMask(Ext.getBody(),{msg:"Searching...",store:resourceStore});
    resourceResult.render('resource-result');

    resourceResult.getSelectionModel().on('rowselect', function(sm, rowIdx, record) {
       searchSet.findById('resource').setValue(record.data.code);
    });

    var searchStore = new Ext.data.Store({
                id: "searchStore",
                proxy: new Ext.data.HttpProxy({
                        url: "service.php",
                        method: 'POST'
                    }),
                reader: new Ext.data.JsonReader({
                        root:"result",
                        idProperty: "ScientificName",
                        totalProperty:"count",
                        fields: darwinFields.map(function(i){ return {name:i}})
                    })
            });

    var searchResult = new Ext.grid.GridPanel({
            colModel: new Ext.grid.ColumnModel([
                    {readOnly: true, dataIndex: 'Family', width: 145,header:"Family", sortable: true},
                    {readOnly: true, dataIndex: 'ScientificName', width: 250, header: "ScientificName", sortable: true}
                ]),
            width: 415,
            height: 400,
            autoScroll: true,
            store: searchStore,
            style: {
                marginTop: "10px",
                marginLeft: "10px",
                marginRight: "10px"
            },
            sm: new Ext.grid.RowSelectionModel({singleSelect: true}),
            title: 'Search Result',
            fbar: [{
                text: "Download CSV",
                handler: function(b,e) {
                    window.open('service.php?csv&query='+ searchStore.baseParams['query'],"csv");
                }
            }]
        });

    var searchLoading = new Ext.LoadMask(Ext.getBody(),{msg:"Searching...",store:searchStore});

    searchResult.getSelectionModel().on('rowselect', function(sm, rowIdx, record) {
            var data = darwinFields.map(function(f) {
                    return [f , record.data[f]];
                });
            searchDetails.getStore().loadData(data);
        });


    searchResult.render('search-result');

    var searchDetails = new Ext.grid.GridPanel({
            colModel: new Ext.grid.ColumnModel([
                    {readOnly: true, dataIndex: 'field', width: 180, header: "Field", sortable: true},
                    {readOnly: true, dataIndex: 'value', width: 350, header: "Value", sortable: true}
                ]),
            ds: new Ext.data.ArrayStore({
                    fields: ['field','value']
                    
                }),
            title: "Record Details",
            width: 550,
            height: 400,
            autoScroll: true,
            style: {
                marginTop: "10px",
                marginLeft: "10px",
                marginRight: "10px",
                fontSize: "12px",
                lineHeight: "20px"
            }
        });

    searchDetails.render('search-details');

    var topPanel = new Ext.Panel ({
            autoHeight: true,
            autoWidth: true,
            border: false,
            layout:"column",
            items: [ searchForm, resourceResult ]
        })
    topPanel.render('top');

    var panel = new Ext.Viewport({
                layout:"border",
                border: false,
                defaults: {
                    border: false,
                },
                items: [
                    {region: "north",contentEl:"top" , layout:"column"},
                    {region: "west",contentEl:"search-result"},
                    {region: "center",contentEl:"search-details" }
                ]
            });

});
