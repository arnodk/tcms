class Tcms {

    constructor() {
        this.token = '';
        this.base = '[baseurl]/index.php';
    }

    setFocus(sId) {
        document.getElementById(sId).focus();
    }

    apiEvent(sId, sEvent, sSystem, sAction, fEvent, fGenerateData, fResult) {
        let oId = document.getElementById(sId);
        let me = this;
        if (sEvent==='click') {
            oId.addEventListener('click',function(event) {
                if (typeof fEvent === 'function') fEvent();
                me.apiCall(sSystem,sAction,fGenerateData(),fResult);
            });
        }
    }

    goto(sSystem,sAction) {
        if (sSystem == "") return;
        let sUrl = this.base + '?system=' + sSystem;
        if (sAction!="") sUrl = sUrl + '&action=' + sAction;
        window.location.href = sUrl;
    }

    alert(sId,sText) {
        let oId = document.getElementById(sId)
        oId.classList.remove("d-none");
        oId.classList.add("alert");
        oId.classList.add("alert-danger");
        oId.innerHTML = 'Invalid session, login or password.';
    }

    clearAlert(sId) {
        let oId = document.getElementById(sId)
        oId.classList.add("d-none");
    }

    apiUrl(sSystem, sAction, sSuffix) {
        let sUrl = this.base + "?_apitoken=" + this.token + "&system=" + sSystem + "&action=" + sAction;
        if (typeof sSuffix != 'undefined') sUrl =sUrl + '&' + sSuffix;
        return sUrl;
    }

    apiCall(sSystem, sAction, oData={}, fResult) {
        var sUrl = this.apiUrl(sSystem, sAction);
        this.postData(sUrl,oData).then(fResult);
    }

    setToken(sToken) {
        this.token = sToken;
    }

    postData(url = '', data = {}) {
        return fetch(url, {
            method: 'POST',
            mode: 'same-origin',
            cache: 'no-cache',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            referrer: 'no-referrer',
            body: JSON.stringify(data)
        })
            .then(response => response.json());
    }

    addCard(sId,sTitle,sSubTitle,sDescription,sLinks) {
        let oId = document.getElementById(sId);
        let card = document.createElement('div');
        card.classList.add('card');
        card.innerHTML = '\
                <div class="card-body">\
                    <h5 class="card-title">'+sTitle+'</h5>\
                    <h6 class="card-subtitle mb-2 text-muted">'+sSubTitle+'</h6>\
                    <p class="card-text">'+sDescription+'</p>\
                    '+sLinks+'\
                </div>\
        ';
        oId.appendChild(card);
    }

    addTagToEditor(sEditorId,sTag, sEndTag) {
        let myField = document.getElementById(sEditorId);
        if (typeof sEndTag === 'undefined') sEndTag = '';
        if (myField.selectionStart || myField.selectionStart == '0') {
            var startPos = myField.selectionStart;
            var endPos = myField.selectionEnd;
            var iLen = endPos - startPos;

            console.log(startPos);
            console.log(endPos);
            console.log(iLen);
            if (startPos === endPos) {
                myField.value = myField.value.substr(0, startPos)
                    + '['
                    + sTag
                    + ']'
                    + '[/'
                    + sEndTag
                    + ']'
                    + myField.value.substr(endPos);
                myField.focus();
                myField.setSelectionRange(startPos + sTag.length + 2,startPos + sTag.length + 2);
            } else {
                myField.value = myField.value.substr(0, startPos)
                    + '['
                    + sTag
                    + ']'
                    + myField.value.substr(startPos,endPos - startPos)
                    + '[/'
                    + sEndTag
                    + ']'
                    + myField.value.substr(endPos);
                myField.focus();
                myField.setSelectionRange(startPos + sTag.length + 2 + endPos - startPos, startPos + sTag.length + 2 + endPos - startPos);
            }
        } else {
            myField.value += sTag + sEndTag;
        }
    }

    attachEditor(sId,sContent) {
        let oId = document.getElementById(sId);
        let sEditorId = sId + 'Editor';
        oId.innerHTML = '<div class="editor-button editor-button-b" id="editor-button-b"><i class="fas fa-bold"></i></div>'
        oId.innerHTML = oId.innerHTML +  '<div class="editor-button editor-button-i" id="editor-button-i"><i class="fas fa-italic"></i></div>'
        oId.innerHTML = oId.innerHTML +  '<div class="editor-button editor-button-h" id="editor-button-h"><i class="fas fa-heading"></i></div>'
        oId.innerHTML = oId.innerHTML + '<textarea id="'+sEditorId+'" class="tcmsEditor form-control">'+sContent+'</textarea>';
        let me = this;
        document.getElementById("editor-button-b").addEventListener('click',function(event) {
            me.addTagToEditor(sEditorId,'B','B')
        });
        document.getElementById("editor-button-i").addEventListener('click',function(event) {
            me.addTagToEditor(sEditorId,'I','I')
        });
        document.getElementById("editor-button-h").addEventListener('click',function(event) {
            me.addTagToEditor(sEditorId,'header','header')
        });
    }

    addButton(sId,sButtonId,fOnClick,sCaption,sMode) {
        let oId = document.getElementById(sId);
        let button = document.createElement('a');
        button.classList.add('btn');
        button.classList.add('btn-'+sMode);
        button.innerHTML = sCaption;
        button.addEventListener('click',fOnClick);
        button.setAttribute("id",sButtonId);
        button.setAttribute("href","javascript://");
        oId.appendChild(button);
    }

    getEditor(sId) {
        return document.getElementById(sId + 'Editor');
    }

    escape(s) {
        s = s.replace("'","\\'");
        s = s.replace('"','\\"');
        return s;
    }
}

let tcms = new Tcms();