function skuSearchData(){
    return{
        searchTerm: '',
        exactMatch: null,
        partialMatches: [],
        loading: false,
        showResults: false,
        selectedProductUrl: '',
        ajaxUrl: 'ajaxskusearch/ajax/search',
        minLength: 2,

        init() {
            this.$watch('searchTerm', (value) => {
                
                this.exactMatch = null;
                
                if (value.length >= this.minLength) {
                    this.searchSku();
                } else {
                    this.partialMatches = [];
                    this.showResults = false;
                }
            });
        },

        searchSku() {
            this.loading = true;
            
            fetch(this.ajaxUrl + '?term=' + encodeURIComponent(this.searchTerm))
                .then(response => response.json())
                .then(data => {
                    this.exactMatch = data.exactMatch;
                    this.partialMatches = data.partialMatches;
                    this.showResults = true;
                    this.loading = false;
                    
                    // Automatically select the first item in the dropdown
                    if (this.partialMatches.length > 0) {
                        this.selectedProductUrl = this.partialMatches[0].url;
                    } else {
                        this.selectedProductUrl = '';
                    }
                })
                .catch(error => {
                    console.error('Error fetching results:', error);
                    this.loading = false;
                });
        },

        goToProduct() {
            if (this.selectedProductUrl) {
                window.location.href = this.selectedProductUrl;
            }
        },
        
        exactMatchProduct(url){
            window.location.href = url;
        }
    };
}


function productsData(){
    return{
        selectedProductUrl: '',
        attributeValue1: '',
        selectattribute1: '',
        selectattribute2: '',
        attributeValue2: '',
        products: [],
        multiselector1 : [],
        multiselector2 : [],
        ajaxUrl: 'ajaxskusearch/ajax/attributefilter',
        attributeurl: 'ajaxskusearch/ajax/attribute',
        loading: false,
        showResults: false,
        timeoutId: null,
        searchbutton : false,

        getProducts(){
            if(this.multiselector1.length > 0 || this.multiselector2.length > 0){
                this.loading = true;
                fetch(this.ajaxUrl + '?attribute=' + encodeURIComponent(this.selectattribute1) + '&value=' + encodeURIComponent(this.multiselector1)+ '&attribute2=' + encodeURIComponent(this.selectattribute2) + '&value2=' + encodeURIComponent(this.multiselector2))
                .then(response => response.json())
                .then(data => {
                    this.products = data.products;
                    this.loading = false;
                    this.showResults = true;
                })
            }else{
                this.loading = true;
                fetch(this.ajaxUrl + '?attribute=' + encodeURIComponent(this.selectattribute1) + '&value=' + encodeURIComponent(this.attributeValue1) + '&attribute2=' + encodeURIComponent(this.selectattribute2) + '&value2=' + encodeURIComponent(this.attributeValue2))
                .then(response => response.json())
                .then(data => {
                    this.products = data.products;
                    this.loading = false;
                    this.showResults = true;
                })
            }
        },

        goToProduct() {
            if (this.selectedProductUrl) {
                window.location.href = this.selectedProductUrl;
            }
        },

    }
}


function attributeData1(){
    return{
        attributeval: [],
        attributetype : '',
        loading: false,

        init() {
            this.$watch('selectattribute1', (value) => {
                this.products = [];
                
                if (value.length >= 0) {
                    this.searchAttribute();
                } else {
                    this.attributeval = [];
                    this.showResults = false;
                }
            });
        },

        searchAttribute() {
            this.loading = true;
            fetch(this.attributeurl + '?attribute=' + encodeURIComponent(this.selectattribute1))
                .then(response => response.json())
                .then(data => {
                    this.attributetype = data.attributetype;
                    this.attributeval = data.attributeval;
                    this.loading = false;
                    this.searchbutton = true;
                })
                .catch(error => {
                    console.error('Error fetching results:', error);
                    this.loading = false;
            });
        }
    }
}


function attributeData2(){
    return{
        attributeval: [],
        attributetype : '',
        loading: false,

        init() {
            this.$watch('selectattribute2', (value) => {
                this.products = [];
                
                if (value.length >= 0) {
                    this.searchAttribute();
                } else {
                    this.attributeval = [];
                    this.showResults = false;
                }
            });
        },

        searchAttribute() {
            this.loading = true;
            fetch(this.attributeurl + '?attribute=' + encodeURIComponent(this.selectattribute2))
                .then(response => response.json())
                .then(data => {
                    this.attributetype = data.attributetype;
                    this.attributeval = data.attributeval;
                    this.loading = false;
                    this.searchbutton = true;
                })
                .catch(error => {
                    console.error('Error fetching results:', error);
                    this.loading = false;
            });
        }
    }
}