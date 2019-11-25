class TcmsPager {

    constructor(data,elList) {
        this.data = data;
        this.elList = elList;
    }

    row(fRow) {
        this.data.data.forEach(fRow);
    }

    static getControlLiItem(fClick, sCaption) {
        let li = document.createElement('li');
        li.classList.add('page-item');
        let a = document.createElement('a');
        a.classList.add('page-link');
        let text = document.createTextNode(sCaption);
        a.appendChild(text);
        a.addEventListener("click",fClick);
        li.appendChild(a);
        return li;
    }

    renderControls(fPageChange) {
        let data = this.data;
        let iPage = parseInt(data.page);
        let maxControlButtonNumber = 10;

        console.log(data);
        let divControls = document.createElement('nav');
        divControls.classList.add('pager_controls');
        let ul = document.createElement('ul');
        ul.classList.add('pagination');
        divControls.appendChild(ul);

        if (parseInt(data.number_of_pages) > 1) {
            // previous button
            if (!data.is_first_page===true) {
                let li = TcmsPager.getControlLiItem(function() {
                    fPageChange(iPage-1);
                },'<');

                ul.appendChild(li);
            }

            // number buttons
            for(let i=1;(i<=data.number_of_pages && i<maxControlButtonNumber);i++) {
                let li = TcmsPager.getControlLiItem(function() {
                    fPageChange(i);
                },i);
                if (i===iPage) li.classList.add('pager_page_active');
                ul.appendChild(li);
            }

            // next button
            if (!data.is_last_page===true) {
                let li = TcmsPager.getControlLiItem(function() {
                    fPageChange(iPage+1);
                },'>');

                ul.appendChild(li);
            }
        }

        this.elList.appendChild(divControls);
    }
}
