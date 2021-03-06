tcms.setToken([newtoken]);

var sSystem = '';
var sAction = '';

window.dashboardFireEvent = function(oLink) {
    sSystem = oLink.getAttribute('data-system');
    sAction = oLink.getAttribute('data-action');

    window.dashboardController(sSystem, sAction);
};

window.pageController = function(result,sAction,oParams) {
    tcms.addButton('content','btnAddPage',function() {
        let sPageName = prompt('Page name:');
        if (sPageName!=null && sPageName!=="") {
            window.dashboardAddPage(tcms.escape(sPageName));
        }
    },'add page','primary');

    if (typeof result.list_data !== 'undefined') {
        let pager = tcms.pager(result.list_data, document.getElementById('content'));
        pager.row(function (item) {
            tcms.addCard('content', item.name, '...', item.summary, '\
                                <a href="javascript://" onclick="dashboardEditPage(this)" data-name="' + item.nameSafe + '" class="card-link btn btn-sm btn-primary">edit</a>\
                                \
                                <a href="javascript://" onclick="dashboardDeletePage(this)" data-name="' + item.nameSafe + '" class="card-link btn btn-sm btn-danger">delete</a>\
            ');
        });
        pager.renderControls(function (iPage) {
            window.dashboardController('page', 'list', {'page': iPage});
        });
    }
};

window.blockController = function(result,sAction,oParams) {
    tcms.addButton('content','btnAddBlock',function() {
        let sBlockName = prompt('Block name:');
        if (sBlockName!=null && sBlockName!="") {
            window.dashboardAddBlock(tcms.escape(sBlockName));
        }
    },'add block','primary');

    if (typeof result.list_data !== 'undefined') {
        let pager = tcms.pager(result.list_data, document.getElementById('content'));
        pager.row(function(item) {
            tcms.addCard('content',item.name,'...',item.summary,'\
                            <a href="javascript://" onclick="dashboardEditBlock(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-primary">edit</a>\
                            \
                            <a href="javascript://" onclick="dashboardDeleteBlock(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-danger">delete</a>\
            ');
        });
        pager.renderControls(function (iPage) {
            window.dashboardController('block', 'list', {'page': iPage});
        });
    }
};

window.templateController = function(result,sAction,oParams) {
    tcms.addButton('content','btnAddTemplate',function() {
        let sTemplateName = prompt('Template name:');
        if (sTemplateName!=null && sTemplateName!="") {
            window.dashboardAddTemplate(tcms.escape(sTemplateName));
        }
    },'add template','primary');

    if (typeof result.list_data !== 'undefined') {
        let pager = tcms.pager(result.list_data, document.getElementById('content'));
        pager.row(function(item) {
            tcms.addCard('content',item.name,'...',item.summary,'\
                            <a href="javascript://" onclick="dashboardEditTemplate(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-primary">edit</a>\
                            \
                            <a href="javascript://" onclick="dashboardDeleteTemplate(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-danger">delete</a>\
            ');
        });
        pager.renderControls(function (iPage) {
            window.dashboardController('template', 'list', {'page': iPage});
        });
    }
};

window.assetController = function(result,sAction,oParams) {
    let oId = document.getElementById('content');

    if (typeof result.form !== 'undefined') {
        let div = document.createElement('div')
        div.innerHTML = result.form;
        oId.append(div);
    }

    if (typeof result.list_data !== 'undefined') {
        let pager = tcms.pager(result.list_data,document.getElementById('content'));
        pager.row(function(item) {
            tcms.addCard('content',item.name,'...',item.summary,'\
                               <a href="javascript://" onclick="dashboardDeleteAsset(this)" data-name="'+item.nameSafe+'" class="card-link btn btn-sm btn-danger">delete</a>\
            ');
        });
        pager.renderControls(function(iPage) {
            window.dashboardController('asset','list',{'page':iPage});
        });
    }

    window.dashboardSetAssetHandlers();
};

window.logController = function(result,sAction,oParams) {

    if (typeof result.list_data !== 'undefined') {
        let pager = tcms.pager(result.list_data,document.getElementById('content'));
        pager.row(function(item) {
            tcms.addCard('content',item.datetime, item.type, '<div class="row dashboard-log-header">' +
                '   <div class="col-sm-12">'+item.addr+'</div>' +
                '</div>' +
                '<div class="row dashboard-body">' +
                '   <div class="col-sm-12">'+item.message+'</div>' +
                '</div>',
            '');
        });
        pager.renderControls(function(iPage) {
            window.dashboardController('log','list',{'page':iPage});
        });
    }

};

window.userController = function(result,sAction,oParams) {

    tcms.addButton('content','btnAddUser',function() {
        let modal = tcms.modal();
        modal.setTitle('Add user');
        modal.addInput('text','name', 'userName');
        modal.addInput('password','password','userPassw');
        modal.addButton('primary','ok','userSave',function(evt) {
            modal.clearFlash();

            let data = modal.getFieldData();
                if (data.userName === '' || data.userPassw === '') {
                    modal.flash('error','No name or password supplied');
                } else if (data.userPassw !== data.userPassw_repeat)  {
                    modal.flash('error','Passwords are not the same');
                } else {
                    window.dashboardAddUser(data);
                }
        });
        modal.render(document.getElementById('content'));
    },'add user','primary');

    if (typeof result.list_data !== 'undefined') {
        let pager = tcms.pager(result.list_data,document.getElementById('content'));
        pager.row(function(item) {
            if (item.name !== 'root') {
                sDeleteLink     = '<a href="javascript://" onclick="dashboardLoginDelete(this)" data-name="'+item.name+'" class="card-link btn btn-sm btn-danger">delete</a>'
                sChangeGroups   = '<a href="javascript://" onclick="dashboardLoginChangeGroups(this)" data-name="'+item.name+'" class="card-link btn btn-sm btn-warning">change groups</a>';
            } else {
                sDeleteLink     = '';
                sChangeGroups   = '';
            }
            tcms.addCard('content',item.name, '', '','' +
                '<a href="javascript://" onclick="dashboardLoginChangePw(this)" data-name="'+item.name+'" class="card-link btn btn-sm btn-warning">change password</a>' +
                sChangeGroups + sDeleteLink);
        });
        pager.renderControls(function(iPage) {
            window.dashboardController('login','list',{'page':iPage});
        });
    }

};

window.dashboardLoginChangeGroups = function(userLink) {
    var user = userLink.dataset.name;
    tcms.apiCall('login','get_groups',{
        'user':user,
    },function(result) {
        let oGroups = result.groups;
        let aCollection = [];
        for(var group in oGroups) {
            aCollection.push({
                id:group,
                caption:group,
                checked:(oGroups[group]==='YES')
            });
        }
        modal = tcms.modal();
        modal.setTitle('Change groups');
        modal.addInput('chkboxcollection', 'groups', 'userGroups', aCollection);
        modal.addButton('primary', 'ok', 'userSave', function (evt) {
            modal.clearFlash();

            let data = modal.getFieldData().userGroups;
            let groups = [];

            data.forEach(function(item) {
                if (item.checked) {
                    groups.push(item.id)
                }
            });

            tcms.apiCall('login','set_groups', {
                'user':user,
                'groups': groups
            });

            modal.close();

        });
        modal.render(document.getElementById('content'));
    })
};

window.dashboardAddUser = function(data) {
    tcms.apiCall('login','add',{
        'user':data.userName,
        'passw':data.userPassw,
        'groups':''
    },function(result) {
        if (result.status==='OK') {
            alert('user added');
            let modal = tcms.modal();
            modal.close();
            window.dashboardController('login','list');
        } else {
            alert('could not add user: ' + result.reason);
        }
    });
};

window.dashboardController = function(sSystem,sAction, oParams) {
    if (typeof oParams === 'undefined') oParams = {};
    tcms.apiCall(sSystem,sAction,oParams,function(result) {
        let oId = document.getElementById('content');
        oId.innerHTML = '';

        if (result.status==='OK') {
            if (sSystem === 'page') {
                pageController(result,sAction,oParams);
            } else if (sSystem==='block') {
                 blockController(result,sAction,oParams);
            } else if (sSystem==='template') {
                 templateController(result,sAction,oParams);
            } else if (sSystem==='asset') {
                assetController(result,sAction,oParams);
            } else if (sSystem==='log') {
                logController(result,sAction,oParams);
            } else if (sSystem==='login') {
                userController(result,sAction,oParams);
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

