class TcmsModal {

    constructor() {
        this.title = '';
        this.fields = [];
        this.buttons = [];
        this.content = false;
    }

    setTitle(sTitle) {
        this.title=sTitle;
    }

    addInput(sType,sCaption,sId) {
        let field = {};
        field.class = '';
        field.type = sType;
        field.caption = sCaption;
        field.id = sId;
        this.fields.push(field);
    }

    addButton(sClass, sCaption, sId, fClick) {
        let button = {};
        button.class = sClass;
        button.caption = sCaption;
        button.id = sId;
        button.click = fClick;
        this.buttons.push(button);
    }

    renderFields() {
        let sResult = '';

        this.fields.forEach(function(field) {
            switch (field.type) {
                case "text":
                    sResult = sResult + `
                        <input id="`+field.id+`" class="form-control dashboard-input dashboard-input-text" type="text" placeholder="`+field.caption+`" />
                    `;
                    break;
                case "password":
                    sResult = sResult + `
                        <input id="`+field.id+`" class="form-control dashboard-input dashboard-input-password" type="password" placeholder="`+field.caption+`" />
                    `;
                    sResult = sResult + `
                        <input id="`+field.id+`_repeat" class="form-control dashboard-input dashboard-input-password-repeat" type="password" placeholder="`+field.caption+` repeat" />
                    `;
                    break;
            }
        });

        return sResult;
    }

    renderButtons() {
        let sResult = '';

        this.buttons.forEach(function(button) {
            sResult = sResult + `
                <button id="`+button.id+`" type="button" class="btn btn-`+button.class+`">`+button.caption+`</button>
            `
        });

        return sResult;
    }

    setEventHandlers() {
        this.buttons.forEach(function (button) {
            document.getElementById(button.id).removeEventListener("click",button.click);
            document.getElementById(button.id).addEventListener("click",button.click);
        });
    }

    render(content) {
        this.content = content;

        let sFields = this.renderFields();
        let sButtons = this.renderButtons();

        this.content.insertAdjacentHTML(
            'afterbegin',
            `<div class="modal show modal-show" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">`+this.title+`</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    `+sFields+`  
                  </div>
                  <div class="modal-footer">
                     `+sButtons+`
                  </div>
                </div>
              </div>
            </div>`
        );

        document.querySelector(".modal .close").addEventListener("click",tcms.modal().close);
        this.setEventHandlers();

    }

    flash(sType,sText) {
        this.clearFlash();

        let sClass='';
        switch(sType) {
            case 'error':
                sClass = 'danger';
                break;
        }
        document.getElementsByClassName('modal-footer')[0].insertAdjacentHTML('beforebegin',`
            <div id="flashMessage" class="alert alert-`+sClass+` " role="alert">
                `+sText+`
            </div>
        `)
    }

    clearFlash() {
        let el = document.getElementById("flashMessage");
        if (el!==null) el.remove();
    }

    getFieldData() {
        // run through all fields, and return their values:
        let data = {};
        this.fields.forEach(function(field) {
            switch (field.type) {
                case "text":
                    data[field.id] = document.getElementById(field.id).value;
                    break;
                case "password":
                    data[field.id] = document.getElementById(field.id).value;
                    data[field.id + "_repeat"] = document.getElementById(field.id + "_repeat").value;
                    break;
            }
        });
        return data;
    }

    close() {
        $(".modal").hide();
        document.getElementsByClassName('modal-footer')[0].remove();
    }
}