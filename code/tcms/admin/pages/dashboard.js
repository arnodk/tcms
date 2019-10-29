tcms.setToken([newtoken]);

var sSystem = '';
var sAction = '';

window.dashboardFireEvent = function(oLink) {
    sSystem = oLink.getAttribute('data-system');
    sAction = oLink.getAttribute('data-action');

    window.dashboardController(sSystem, sAction);
};

window.dashboardController = function(sSystem,sAction, oParams) {
    if (typeof oParams === 'undefined') oParams = {};
    tcms.apiCall(sSystem,sAction,oParams,function(result) {
        let oId = document.getElementById('content');
        oId.innerHTML = '';

        if (result.status==='OK') {
            if (sSystem === 'page') {
                tcms.addButton('content','btnAddPage',function() {
                    var sPageName = prompt('Page name:');
                    if (sPageName!=null && sPageName!="") {
                        window.dashboardAddPage(tcms.escape(sPageName));
                    }
                },'add page','primary');

                let items = result.pages;
                if (Array.isArray(items)) {
                    items.forEach(function(item) {
                        tcms.addCard('content',item.name,'...',item.summary,'\
                            <a href="javascript://" onclick="dashboardEditPage(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-primary">edit</a>\
                            \
                            <a href="javascript://" onclick="dashboardDeletePage(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-danger">delete</a>\
                        ');
                    });
                }
            } else if (sSystem==='block') {
                 tcms.addButton('content','btnAddBlock',function() {
                    var sBlockName = prompt('Block name:');
                    if (sBlockName!=null && sBlockName!="") {
                        window.dashboardAddBlock(tcms.escape(sBlockName));
                    }
                 },'add block','primary');

                 let items = result.blocks;
                 if (Array.isArray(items)) {
                    items.forEach(function(item) {
                        tcms.addCard('content',item.name,'...',item.summary,'\
                            <a href="javascript://" onclick="dashboardEditBlock(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-primary">edit</a>\
                            \
                            <a href="javascript://" onclick="dashboardDeleteBlock(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-danger">delete</a>\
                        ');
                    });
                 }
            } else if (sSystem==='template') {
                 tcms.addButton('content','btnAddTemplate',function() {
                    var sTemplateName = prompt('Template name:');
                    if (sTemplateName!=null && sTemplateName!="") {
                        window.dashboardAddTemplate(tcms.escape(sTemplateName));
                    }
                 },'add template','primary');

                 let items = result.templates;
                 if (Array.isArray(items)) {
                    items.forEach(function(item) {
                        tcms.addCard('content',item.name,'...',item.summary,'\
                            <a href="javascript://" onclick="dashboardEditTemplate(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-primary">edit</a>\
                            \
                            <a href="javascript://" onclick="dashboardDeleteTemplate(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-danger">delete</a>\
                        ');
                    });
                 }
            } else if (sSystem==='asset') {

                if (typeof result.form != 'undefined') {
                    var div = document.createElement('div')
                    div.innerHTML = result.form;
                    oId.append(div);
                }

                if (typeof result.assets != 'undefined') {
                    let items = result.assets;
                    if (Array.isArray(items)) {
                       items.forEach(function(item) {
                           tcms.addCard('content',item.name,'...',item.summary,'\
                               <a href="javascript://" onclick="dashboardDeleteAsset(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-danger">delete</a>\
                           ');
                       });
                    }
                }

                window.dashboardSetAssetHandlers();
            }
        }
    });
};

window.dashboardUploadFile = function(evt) {
    var files = document.getElementById('files').files;

    // prepare statistics:
    var iTotalSize = 0;
    var iTotalUploaded = 0;
    for (var j = 0; j <files.length; j++) {
        var blob = files[j];
        iTotalSize = iTotalSize + blob.size;
    }

    document.getElementById('totalSizeCaption').innerHTML = iTotalSize;

    for (var j = 0; j <files.length; j++) {
        var blob = files[j];
        console.log(blob);
        console.log('chunking');
        const BYTES_PER_CHUNK = 1024 * 1024;
        // 1MB chunk sizes.
        const SIZE = blob.size;

        var start = 0;
        var end = BYTES_PER_CHUNK;
        var sName = blob.name;

        while (start < SIZE) {

            var chunk = blob.slice(start, end);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', tcms.apiUrl('asset','upload','name=' + encodeURIComponent(sName) + '&size=' + SIZE + '&start=' + start), false);
            xhr.onload = function(e) {};
            xhr.send(blob);

            iTotalUploaded = iTotalUploaded + (Math.min(end,SIZE) - start);

            console.log('uploaded: ' + iTotalUploaded);
            // update progress bar:
            let iFraction = (iTotalUploaded / iTotalSize) * 100;
            document.getElementById("upload-progress").style.width = iFraction + '%';
            document.getElementById("uploadedSizeCaption").innerHTML = iTotalUploaded;

            start = end;
            end = start + BYTES_PER_CHUNK;

        }
    }
};

window.dashboardSetAssetHandlers = function() {
    document.getElementById('files').addEventListener('change', window.dashboardUploadFile, false);
};

window.dashboardEditPage = function(link) {
    var sName = link.getAttribute('data-name');

    tcms.apiCall('page','edit',{
             'page':sName
    },function(result) {
            var oId = document.getElementById('content');
            oId.innerHTML = '';
            if (result.status==='OK') {
                if (typeof result.content != 'undefined') {
                    tcms.attachEditor('content', result.content);
                    tcms.getEditor('content').setAttribute('data-page-name',sName);
                    tcms.addButton('content','btnSavePage',window.savePage,'save','primary');
                }
            }
    });
};

window.dashboardDeletePage = function(link) {
    var sName = link.getAttribute('data-name');
    if (confirm('Are you sure you want to delete page "' + sName + '"?')) {
        tcms.apiCall('page','delete',{
                     'page':sName
        },function(result) {
                    var oId = document.getElementById('content');
                    oId.innerHTML = '';
                    if (result.status==='OK') {
                        alert('page deleted');
                        window.dashboardController('page','list');
                    }
        });
    }
};

window.dashboardEditBlock = function(link) {
    var sName = link.getAttribute('data-name');

    tcms.apiCall('block','edit',{
             'block':sName
    },function(result) {
            var oId = document.getElementById('content');
            oId.innerHTML = '';
            if (result.status==='OK') {
                if (typeof result.content != 'undefined') {
                    tcms.attachEditor('content', result.content);
                    tcms.getEditor('content').setAttribute('data-block-name',sName);
                    tcms.addButton('content','btnSaveBlock',window.saveBlock,'save','primary');
                }
            }
    });
};

window.dashboardDeleteBlock = function(link) {
    var sName = link.getAttribute('data-name');
    if (confirm('Are you sure you want to delete block "' + sName + '"?')) {
        tcms.apiCall('block','delete',{
                     'block':sName
        },function(result) {
                    var oId = document.getElementById('content');
                    oId.innerHTML = '';
                    if (result.status==='OK') {
                        alert('block deleted');
                        window.dashboardController('block','list');
                    }
        });
    }
};

window.dashboardEditTemplate = function(link) {
    var sName = link.getAttribute('data-name');

    tcms.apiCall('template','edit',{
             'template':sName
    },function(result) {
            var oId = document.getElementById('content');
            oId.innerHTML = '';
            if (result.status==='OK') {
                if (typeof result.content != 'undefined') {
                    tcms.attachEditor('content', result.content);
                    tcms.getEditor('content').setAttribute('data-template-name',sName);
                    tcms.addButton('content','btnSaveTemplate',window.saveTemplate,'save','primary');
                }
            }
    });
};

window.dashboardDeleteTemplate = function(link) {
    var sName = link.getAttribute('data-name');
    if (confirm('Are you sure you want to delete template "' + sName + '"?')) {
        tcms.apiCall('template','delete',{
                     'template':sName
        },function(result) {
                    var oId = document.getElementById('content');
                    oId.innerHTML = '';
                    if (result.status==='OK') {
                        alert('template deleted');
                        window.dashboardController('template','list');
                    }
        });
    }
};

window.dashboardDeleteAsset = function(link) {
    var sName = link.getAttribute('data-name');
    if (confirm('Are you sure you want to delete asset "' + sName + '"?')) {
        tcms.apiCall('asset','delete',{
                     'asset':sName
        },function(result) {
                    var oId = document.getElementById('content');
                    oId.innerHTML = '';
                    if (result.status==='OK') {
                        alert('asset deleted');
                        window.dashboardController('asset','list');
                    }
        });
    }
};

window.dashboardAddPage = function(sName) {
    document.getElementById('content').innerHTML = '';

    tcms.attachEditor('content', '');
    tcms.getEditor('content').setAttribute('data-page-name',sName);
    tcms.addButton('content','btnSavePage',window.savePage,'save','primary');
};

window.savePage = function() {
    var sContent = tcms.getEditor('content').value;
    var sName = tcms.getEditor('content').getAttribute('data-page-name')

    tcms.apiCall('page','save',{
             'page':sName,
             'content':sContent
    },function(result) {
            var oId = document.getElementById('content');
            oId.innerHTML = '';
            if (result.status==='OK') {
                alert('content saved');
                window.dashboardController('page','list');
            }
    });
};

window.saveBlock = function() {
    var sContent = tcms.getEditor('content').value;
    var sName = tcms.getEditor('content').getAttribute('data-block-name')

    tcms.apiCall('block','save',{
             'block':sName,
             'content':sContent
    },function(result) {
            var oId = document.getElementById('content');
            oId.innerHTML = '';
            if (result.status==='OK') {
                alert('content saved');
                window.dashboardController('block','list');
            }
    });
};

window.saveTemplate = function() {
    var sContent = tcms.getEditor('content').value;
    var sName = tcms.getEditor('content').getAttribute('data-template-name')

    tcms.apiCall('template','save',{
             'template':sName,
             'content':sContent
    },function(result) {
            var oId = document.getElementById('content');
            oId.innerHTML = '';
            if (result.status==='OK') {
                alert('content saved');
                window.dashboardController('template','list');
            }
    });
};

window.dashboardAddBlock = function(sName) {
    document.getElementById('content').innerHTML = '';

    tcms.attachEditor('content', '');
    tcms.getEditor('content').setAttribute('data-block-name',sName);
    tcms.addButton('content','btnSaveBlock',window.saveBlock,'save','primary');
};

window.dashboardAddTemplate = function(sName) {
    document.getElementById('content').innerHTML = '';

    tcms.attachEditor('content', '');
    tcms.getEditor('content').setAttribute('data-template-name',sName);
    tcms.addButton('content','btnSaveTemplate',window.saveTemplate,'save','primary');
};

