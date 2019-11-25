class TcmsModal {

    constructor() {
        this.title = '';
        this.fields = [];
        this.buttons = [];
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
                        <input class="form-control" placeholder="`+field.caption+`" />
                    `;
                    break;
            }
        });

        return sResult;
    }

    renderButtons() {
        return '';
    }

    render(content) {
        let sFields = this.renderFields();
        let sButtons = this.renderButtons();
        content.insertAdjacentHTML(
            'afterbegin',
            `<div class="modal show modal-show" tabindex="-1" role="dialog">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">`+this.title+`</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <p>Modal body text goes here.</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>`
        );

    }
}