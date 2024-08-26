<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-slot name="header">
        @if($type == 'out')   
            {{ __('TRANSAKSI PENJUALAN') }}
        @else
            {{ __('TRANSAKSI PEMBELIAN') }}
        @endif
    </x-slot>

    <div class="container-fluid">
       
            <div class="card">
    
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <label for="date">Tanggal</label>
                            <input type="date" name="date" value="{{ date('Y-m-d')}}" id="date" class="form-control">

                           
                        </div>
                        <div class="col-4">
                        @if($type == 'out')   
                            <label for="date">Customer</label>
                            <select name="" class="form-control" id="customer_id"></select> 
                        @else
                            <label for="date">Supplier</label>
                            <select name="" class="form-control" id="supplier_id"></select> 
                        @endif
                        </div>
                        <!-- <div class="col-4">
                            <label for="date">PRODUK</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" disabled aria-describedby="button-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="button-addon2">CARI</button>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-4">
                            <label for="date">NO TRANSAKSI</label>
                            <input type="transaction_number" value="AUTO GENERATED" name="transaction_number" id="transaction_number" disabled class="form-control">
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-header">
                            PRODUK
                        </div>
                        <div class="card-body">
                             
                            <div class="input-group mb-3" style="width:20%;">
                                <!-- <input type="text" class="form-control" id="product_id" placeholder="Masukan Kode Produk" disabled aria-describedby="button-addon2"> -->
                                 <select id="product_id" name="product_id" class="form-control">

                                </select>
                                <!-- <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="button-addon2">CARI</button>
                                </div> -->
                            </div>
                            
                            <table class="table table-bordered">
                                <thead>
                                    <th>KODE</th>
                                    <th>PRODUK</th>
                                    <th>SATUAN</th>
                                    <th>HARGA</th>
                                    <th>QTY</th>
                                    <th>SUB TOTAL</th>
                                    <th>DISKON</th>
                                    <th>TOTAL</th>
                                </thead>
                                <tbody id="product-tbody">
                                    <tr>
                                        <td colspan="9" class="text-center bg-light">No Items</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <th>KODE</th>
                                    <th>PRODUK</th>
                                    <th>SATUAN</th>
                                    <th>HARGA</th>
                                    <th>QTY</th>
                                    <th>SUB TOTAL</th>
                                    <th>DISKON</th>
                                    <th>TOTAL</th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <input type="hidden" name="type" id="type" value="{{ $type }}">
                    <!-- <div class="mb-3">
                        <label for="product_id" class="form-label">Product</label>
                        <select id="product_id" name="product_id" class="form-control">

                        </select>
                    </div> -->
                    <div class="row">
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="sub_total" class="form-label">Sub Total</label>
                                <input type="number" name="sub_total" id="sub_total" disabled class="form-control">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="discount" class="form-label">Diskon</label>
                                <input type="number" name="discount" value="0" id="discount" class="form-control">
                            </div>
                            
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <label for="total" class="form-label">TOTAL</label>
                                <input type="number" name="total" id="total" disabled class="form-control">
                            </div>

                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label for="name" class="form-label">Catatan</label>
                           <textarea name="notes" id="notes" class="form-control"></textarea>
                        </div>
                    </div>
                    

                </div>
                <div class="card-footer">
                    <button type="button" id="btn-submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        
    </div>

    @push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

    @endpush
    @push('js')
        
        <script defer>
            
            
            
            $(document).ready(function() {
                    const productTbody = document.getElementById('product-tbody');
                    const subTotalEl = document.getElementById('sub_total');
                    const totalEl = document.getElementById('total');
                    const discountEl = document.getElementById('discount');
                    let productList = [];

                    const setTransactionTotal = ()=>{
                        totalEl.value = (Number(subTotalEl.value) || 0) - (Number(discountEl.value) || 0);
                    }

                    const setTransactionSubTotal = ()=>{
                        let subTotal = productList.reduce((accumulator,currentValue) => accumulator + currentValue.total, 0);

                        console.log(subTotal)
                        subTotalEl.value = subTotal;

                        setTransactionTotal();
                    }

                    discountEl.addEventListener('change',()=>{
                        setTransactionSubTotal();
                    })

                    const generateTable = ()=>{
                        let html = ``;
                        // let total = 0;
                        // let subTotalEl;
                        for (let i = 0; i < productList.length; i++) {
                            const product = productList[i];
                            let optionsHTML = ``
                            let priceDefault;
                            let productPriceDefault;
                            product?.productprices?.map(price=>{
                                if(price.is_default){
                                    priceDefault = price.price;
                                    productPriceDefault = price.id;
                                }
                                let option = `<option data-price="${price.price}" value='${price.id}' ${price.is_default && 'selected'}>${price.productunit.name} ${price.is_default ? '- DEFAULT' : ''}</option>`
                                optionsHTML = optionsHTML + option;
                            })
                            html += `<tr>
                                <td>${product.code}</td>
                                <td>${product.name}</td>
                                <td>
                                    <select class="form-control product-select-price" id="product-price-id-${product.code}" data-code="${product.code}"> ${optionsHTML}</select>
                                </td>
                                <td>
                                    <input type='number' value="${priceDefault}" class="form-control product-price" disabled id="product-price-${product.code}"/>
                                </td>
                                <td>
                                    <input type='number' value="1" class="form-control product-qty" id="product-qty-${product.code}" data-code="${product.code}"/>
                                </td>
                                <td>
                                    <input type='number' value="${ priceDefault * 1 || 0 }" disabled class="form-control" id="product-sub-total-${product.code}"/>
                                </td>
                                <td>
                                    <input type='number' class="form-control product-discount" value="0" id="product-discount-${product.code}" data-code="${product.code}"/>
                                </td>
                                <td>
                                    <input type='number' value="${ priceDefault * 1 || 0 }" disabled class="form-control" id="product-total-${product.code}"/>
                                </td>
                            </tr>`;

                            // changeProductSubTotal(product.code)
                            productList[i].price = priceDefault * 1 || 0;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
                            productList[i].qty = 1;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
                            productList[i].discount = 0;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
                            productList[i].subTotal = priceDefault * 1 || 0;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  
                            productList[i].total = priceDefault * 1 || 0;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
                            productList[i].product_price_id = productPriceDefault;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
                        }
                        
                        productTbody.innerHTML = html;
                        setTransactionSubTotal();
                    }

                    function changeProductSubTotal(code){
                        const qty = document.getElementById('product-qty-'+code)
                        const price = document.getElementById('product-price-'+code)
                        const discount = document.getElementById('product-discount-'+code)
                        const productPriceSelectOption = document.getElementById('product-price-id-'+code);
                        const subTotalProduct = document.getElementById('product-sub-total-'+code)
                        const totalProduct = document.getElementById('product-total-'+code)

                        let subTotal = Number(price.value) * Number(qty.value);
                        let total = subTotal - Number(discount.value);
                        // update productList
                        let productListSelectedIndex = productList.findIndex((product)=>{return product.code === code})

                        productList[productListSelectedIndex].qty = qty.value;
                        productList[productListSelectedIndex].price = price.value;
                        productList[productListSelectedIndex].subTotal = subTotal;
                        productList[productListSelectedIndex].total = total;
                        productList[productListSelectedIndex].discount = discount.value;
                        productList[productListSelectedIndex].product_price_id = productPriceSelectOption.value;

                        // console.log(productList)

                        subTotalProduct.value = subTotal;
                        totalProduct.value = total;

                        setTransactionSubTotal()
                    }

                    $('#product-tbody').on('change',function(e){
                        const target = e.target;
                        const productCode = target.dataset.code;
                        if(target.classList.contains('product-select-price')){
                            const priceInput = document.getElementById("product-price-"+productCode);
                            // console.log(priceInput);
                            const price = target.options[target.selectedIndex].dataset.price;
                            priceInput.value = price

                            changeProductSubTotal(productCode)
                        }
                        if(target.classList.contains('product-qty')){
                            changeProductSubTotal(productCode)
                        }
                        if(target.classList.contains('product-discount')){
                            changeProductSubTotal(productCode)
                        }
                    })

                    $('#product_id').select2({
                        theme: "bootstrap4",
                        ajax: {
                            url: '/stok/search', // URL to fetch data
                            dataType: 'json',
                            delay: 250,
                            processResults: function (data) {
                                return {
                                    results: $.map(data, function (item) {       
                                        return {
                                            text: `${item.code} - ${item.name}`, 
                                            id: item.id,
                                            // productprices : item.productprices,
                                            // product_code : item.code,
                                            item : item
                                        }
                                    })
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 1, 
                        placeholder: 'Search for a product',
                        allowClear: true
                    });

                    $('#customer_id').select2({
                        theme: "bootstrap4",
                        ajax: {
                            url: '/customer/search', // URL to fetch data
                            dataType: 'json',
                            delay: 250,
                            processResults: function (data) {
                                return {
                                    results: $.map(data, function (item) {       
                                        return {
                                            text: `${item.name}`, 
                                            id: item.id,
                                            // productprices : item.productprices,
                                            // product_code : item.code,
                                            // item : item
                                        }
                                    })
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 1, 
                        placeholder: 'Search Customer',
                        allowClear: true
                    });

                    $('#supplier_id').select2({
                        theme: "bootstrap4",
                        ajax: {
                            url: '/supplier/search', // URL to fetch data
                            dataType: 'json',
                            delay: 250,
                            processResults: function (data) {
                                return {
                                    results: $.map(data, function (item) {       
                                        return {
                                            text: `${item.name}`, 
                                            id: item.id,
                                            // productprices : item.productprices,
                                            // product_code : item.code,
                                            // item : item
                                        }
                                    })
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 1, 
                        placeholder: 'Search Supplier',
                        allowClear: true
                    });

                    $('#product_id').on('select2:select', function (e) {
                        var data = e.params.data;
                        // let productprices = data.productprices;
                        
                        productList.push(data.item)
                        generateTable()
                    });

                    async function saveTransaction(data){
                        // let url = 'http://localhost:8000/transaksi/save';
                        let crsfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        let url = '/transaksi/save';

                        $.ajax({
                            url: url,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            contentType: 'application/json',
                            data: JSON.stringify(data),
                            success: function(response) {
                                console.log('result', response);
                                // if(!response.status){
                                //     alert(resp)
                                // }
                                alert('Transaction saved successfully!');
                            },
                            error: function(xhr, status, error) {
                                if (xhr.status === 302) {
                                    window.location.href = xhr.getResponseHeader('Location');
                                } else {
                                    console.log('Error:', error);
                                    console.log('Response:', xhr.responseJSON);
                                    alert(xhr.responseJSON.message);
                                }
                            }
                        });
                    }

                    $('#btn-submit').on('click',function(e){
                        e.preventDefault();
                        let data = {
                            date : $('#date').val(),
                            type : $('#type').val(),
                            customer_id : $('#customer_id').val(),
                            supplier_id : $('#supplier_id').val(),
                            items : productList,
                            sub_total : $('#sub_total').val(),
                            discount : $('#discount').val(),
                            total : $('#total').val(),
                        }

                        console.log(data)

                        saveTransaction(data)
                    })
                });


        </script>
    @endpush
</x-app-layout>
