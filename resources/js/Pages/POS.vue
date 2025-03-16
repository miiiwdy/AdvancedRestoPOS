    <script setup>
    import { router } from '@inertiajs/vue3';
    import { ref, onMounted, computed, watch } from "vue";
    import FlashMessage from '@/Components/FlashMessage.vue';

    const props = defineProps({
        product: Array,
        kategori: Array,
        namaKasir: String,
        pajak: Number,
        payment: Array,
        diskonThresholdByOrder: Array,
        diskonThresholdByProduct: Array,
        diskonPercentageByOrder: Array,
        kategoriDiskons: Array,
        table: Array,
        trackOrder: Array,
        dataOrder: Array,
        dataOrderProduct: Array,
    })
    onMounted(() => {
        updateDateTime();
        setInterval(updateDateTime, 1000);
        typeEffect();
        const orderList = document.getElementById("order-list");
        if (orderList) {
            orderList.addEventListener("scroll", onScroll);
        }
        const productList = document.getElementById("product-list");
        if (productList) {
            productList.addEventListener("scroll", onScroll);
        }
    });

    const allActiveDiscounts = computed(() => {
        const kategoriDiskonMap = new Map(props.kategoriDiskons.map(k => [k.id, k.nama_kategori_diskon]));
        return [
            ...(props.diskonThresholdByProduct?.map(diskon => ({
                ...diskon, type: 'product',nama_kategori_diskon: kategoriDiskonMap.get(diskon.kategori_diskons_id) || "Kategori Tidak Ditemukan" 
            })) || []),
            ...(props.diskonThresholdByOrder?.map(diskon => ({
                ...diskon, type: 'order', nama_kategori_diskon: kategoriDiskonMap.get(diskon.kategori_diskons_id) || "Kategori Tidak Ditemukan"
            })) || []),
            ...(props.diskonPercentageByOrder?.map(diskon => ({
                ...diskon, type: 'percentage', nama_kategori_diskon: kategoriDiskonMap.get(diskon.kategori_diskons_id) || "Kategori Tidak Ditemukan"
            })) || [])
        ];
    });

    const isAnyDiscountApplied = computed(() => {
        return allActiveDiscounts.value.some(diskon => 
            cart.value.some(item => item.note === diskon.nama_diskon)
        );
    });

    const usedDiscounts = computed(() => {
        const filtered = allActiveDiscounts.value.filter(diskon => 
            cart.value
                .filter(item => item.note && item.note.trim() !== "")
                .some(item => {
                    return item.note.trim().toLowerCase() === diskon.nama_diskon.trim().toLowerCase();
                })
        );
        return filtered;
    });

    const time = ref("");
    const period = ref("");
    const date = ref("");
    const activeMenu = ref(1337);
    const searchQuery = ref('');
    const searchQueryTables = ref('');
    const isModalOpen = ref(false);
    const isCartNoteModalOpen = ref(false);
    const isPaymentModalOpen = ref(false);
    const isAmountPaidModalOpen = ref(false);
    const isGuestEditModalOpen = ref(false);
    const isChangeModalOpen = ref(false);
    const isCheckDiscountModalOpen = ref(false);
    const isTableModalOpen = ref(false);
    const isTrackOrderModalOpen = ref(false);
    const isTrackOrderOpen = ref(true);
    const selectedOrder = ref(null);
    const selectedProduct = ref(null);
    const selectedCartProduct = ref(null);
    const quantity = ref(1);
    const note = ref('');
    const cart = ref([]);
    const showCart = ref(true);
    const orderID = ref('');
    const orderType = ref('Dine In')
    const tableData = ref('');
    const isTableActive = ref(true);
    const guest = ref(0);
    const paymentData = ref('Payment');
    const placeholderText = "Search something sweet on your mind here...";
    const displayPlaceholder = ref("");
    const rounding = ref(0);
    const totalAfterRounding = ref(0);
    const amountPaid = ref(0);
    const change = ref(0);
    const ordersPerPage = 10;
    const loadedOrders = ref(ordersPerPage);
    const productsPerPage = 20;
    const loadedProducts = ref(productsPerPage);
    var isCooldown = false;
    var index = 0;
    var isDeleting = false;
    var isConfirmPayment = false;
 
    const toggleCart = () => {
        showCart.value = !showCart.value;
    };

    const toggleGuest = () => {
        if (isConfirmPayment) return;
        if (!isGuestEditModalOpen.value) {
            isGuestEditModalOpen.value = true;
        }
        else {
            isGuestEditModalOpen.value = false;
        }
    }

    const toggleTrackOrder = () => {
        if (isTrackOrderOpen.value) {
            isTrackOrderOpen.value = false;
        }
        else {
            isTrackOrderOpen.value = true;
        }
    }

    const formattedDataOrderUpdatedAt = computed(() => {
        if (!selectedOrder.value?.updated_at) return "";
        const date = new Date(selectedOrder.value.updated_at);
        return new Intl.DateTimeFormat("en-EN", {
            weekday: "short",
            month: "short",
            day: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: false,
        }).format(date);
    });

    const openTrackOrderModal = (order) => {
        selectedOrder.value = order;
        isTrackOrderModalOpen.value = true;
    };

    const closeTrackOrderModal = () => {
        isTrackOrderModalOpen.value = false;
        selectedOrder.value = null;
    };

    const selectedProducts = computed(() => {
        if (!selectedOrder.value) return [];
            return props.dataOrderProduct.filter((product) => product.order_id === selectedOrder.value.order_id
        );
    });

    const storeGuest = () => {
        if (guest.value === 0) {
            console.log('guest value gabisa 0, tmbahin dong');
            return;
        }
        cart.value.forEach(item => {
                item.guest = guest.value;
            });
        isGuestEditModalOpen.value = false;
    }

    const openModal = (product) => {
        if (isConfirmPayment) return;
        selectedProduct.value = product;
        isModalOpen.value = true;
        const existingProductIndex = cart.value.findIndex(item => item.kode_product === selectedProduct.value.kode_product);
        if (existingProductIndex !== -1) {
            alert('produk sudah ada di keranjang')
            isModalOpen.value = false;
                return;
            }
    };

    const closeModal = () => {
        isModalOpen.value = false;
        selectedProduct.value = null;
        note.value = '';
        quantity.value = 1;
    };

    const openCartNoteModal = (item) => {
        if (isConfirmPayment) return;
        if (isCartNoteModalOpen.value && selectedCartProduct.value === item) {
            isCartNoteModalOpen.value = false;
            selectedCartProduct.value = null;
        } 
        else {
            selectedCartProduct.value = item;
            isCartNoteModalOpen.value = true;
        }
    };

    const saveNote = () => {
        if (selectedCartProduct.value) {
            selectedCartProduct.value.note = note.value;
        }
        isCartNoteModalOpen.value = false;
        selectedCartProduct.value = null;
    }

    const closeCartNoteModal = () => {
        if (isConfirmPayment) return;
        isCartNoteModalOpen.value = false;
        selectedCartProduct.value = null;
        note.value = '';
    };

    watch(selectedCartProduct, (newValue) => {
        note.value = newValue && newValue.note ? newValue.note : '';
    }, { deep: true });

    const storeAmountPaid = () => {
        const amountPaidNumber = Number(amountPaid.value.replace(/\D/g, ""));
        const changeData = amountPaidNumber - totalRounding();
        change.value = changeData;
        
        if (totalRounding() < amountPaidNumber) {
            cart.value.forEach(item => {
                item.amount_paid = amountPaidNumber;
                item.change = changeData;
            });
            console.log('kembalian');
            amountPaid.value = 0;
            isAmountPaidModalOpen.value = false;
            isChangeModalOpen.value = true;
        } 
        else if (totalRounding() === amountPaidNumber) {
            cart.value.forEach(item => {
                item.amount_paid = amountPaidNumber;
            });
            console.log('pas');
            amountPaid.value = 0;
            isAmountPaidModalOpen.value = false;
            console.log(cart);
            
        } 
        else {
            console.warn('uangnya kurang');
        }
    };

    const closeChangeModal = () => {
        isChangeModalOpen.value = false;
        change.value = 0;
    }

    const closeCheckDiscountModal = () => {
        isCheckDiscountModalOpen.value = false;
    };

    const openCheckDiscountModal = () => {
        !isCheckDiscountModalOpen.value ? isCheckDiscountModalOpen.value = true : isCheckDiscountModalOpen.value = false;
    };
    
    const closePaymentModal = () => {
        isPaymentModalOpen.value = false;
    };
    
    const openPaymentModal = () => {
        if(isConfirmPayment) return;
        !isPaymentModalOpen.value ? isPaymentModalOpen.value = true : isPaymentModalOpen.value = false;

    };
    const confirmPayment = (selectedPayment) => {
        if (!selectedPayment || !selectedPayment.payment_name) {
            console.warn("Payment method not selected 1");
            isPaymentModalOpen.value = false;
            return;
        }
        if (guest.value === 0) {
            console.log('guest value masih kosong');
            isPaymentModalOpen.value = false;
            isGuestEditModalOpen.value = true
            return;
        }
        if (orderType.value === 'Dine In' && tableData.value === '') {
            console.log('tableData value masih kosong');
            isPaymentModalOpen.value = false;
            isTableModalOpen.value = true
            return;
        }
        if (cart.value.length === 0) {
            console.warn("cart lo kosong");
            isPaymentModalOpen.value = false;
            return;
        }
        else {
            isConfirmPayment = true;
            paymentData.value = selectedPayment.payment_name;
            cart.value.forEach(item => {
                item.payment = paymentData.value;
            });
            isPaymentModalOpen.value = false;
            isAmountPaidModalOpen.value = true;
        }
    };

    const closeTableModal = () => {
        isTableModalOpen.value = false;
    };
    const openTableModal = () => {
        if (isConfirmPayment) return;
        !isTableModalOpen.value ? isTableModalOpen.value = true : isTableModalOpen.value = false;

    };
    const confirmTable = (selectedTable) => {
        if (!selectedTable || !selectedTable.nomor_meja) {
            console.warn("Table not selected");
            isTableModalOpen.value = false;
            return;
        }
        else {
            tableData.value = selectedTable.nomor_meja;
            cart.value.forEach(item => {
                item.table = tableData.value;
            });
            isTableModalOpen.value = false;
        }
    };

    const calculateSubtotal = () => cart.value.reduce((sum, item) => sum + item.tt_b, 0);
    const calculateTotalPajak = () => cart.value.reduce((sum, item) => sum + item.total_pajak, 0);
    const calculateTotal = () => cart.value.reduce((sum, item) => sum + item.tt_a, 0);
    
    const totalRounding = () => {
        totalAfterRounding.value = calculateTotal() > 0 ? Math.round(calculateTotal() / 500) * 500 : 0;
        cart.value.forEach(item => {
            item.total_after_rounding = totalAfterRounding.value;
        });
        return calculateTotal() > 0 ? Math.round(calculateTotal() / 500) * 500 : 0;
    }
    const roundingAmount = () => {
        const total = calculateTotal();
        const roundedTotal = totalRounding();
        rounding.value = roundedTotal - total;

        console.log("calculateTotal:", total);
        console.log("totalRounding:", roundedTotal);
        cart.value.forEach(item => {
            item.rounding = rounding.value;
        });
        return roundedTotal - total;
    }
    
    function createorderID() {
        if (!orderID.value) {
            const hurup = Math.random().toString(36).replace(/[^a-z]/g, '').substring(0, 3).toUpperCase();
            const angka = Math.floor(Math.random() * 900) + 100;
            const ON = hurup + angka;
            orderID.value = ON;
            return ON
        }
        else {
            return orderID.value;
        }   
    }

    const getOrderType = () => {
        if (isConfirmPayment) return;
        if (orderType.value === 'Dine In') {
            orderType.value = 'Take Away';
            isTableActive.value = false;
            cart.value.forEach(item => {
                item.orderType = orderType.value;
            });
            if (isTableModalOpen) {
                isTableModalOpen.value = false;
            }
            console.log(orderType.value);
        }
        else if (orderType.value === 'Take Away') {
            orderType.value = 'Dine In';
            isTableActive.value = true;
            cart.value.forEach(item => {
                item.orderType = orderType.value;
            });
            console.log(orderType.value);
        }
    }

    const increaseQty = () => {
        quantity.value++
    };

    const decreaseQty = () => {
        if (quantity.value > 1) {
            quantity.value--;
        }
    };

    const increaseExistQty = (item) => {
        if (isConfirmPayment) return;
        const cartItem = cart.value.find(cartItem => cartItem.id_product === item.id_product); 
        if (cartItem) {
            cartItem.quantity++;
            cartItem.tt_b = cartItem.hj * cartItem.quantity;
            cartItem.total_pajak = cartItem.tt_b * (props.pajak / 100);
            cartItem.tt_a = cartItem.tt_b + cartItem.total_pajak;
            console.log(cart);

            const increaseApplicableDiscountProduct = props.diskonThresholdByProduct.filter((diskonProduct) =>
                cartItem.id_product === diskonProduct.product_id &&
                cartItem.quantity === diskonProduct.minimum_items_count
            );

            const isDiskonOrderExist = cart.value.some(c => 
                props.diskonThresholdByOrder.some(diskon => c.note === diskon.nama_diskon)
            );

            const isDiskonProductExist = cart.value.some(c => c.note === props.diskonThresholdByProduct.nama_diskon);
            
            if (!isDiskonOrderExist) {
                runDiskonByOrderValidation();
            }

            if (!isDiskonProductExist) {
            increaseApplicableDiscountProduct.forEach((diskonProduct) => {
                const relatedDiskonProductItem = props.product.find(p => p.id === diskonProduct.target_product_id) || {};
                cart.value.unshift({
                    np: relatedDiskonProductItem.nama_product || "Tidak Ditemukan",
                    kode_product: relatedDiskonProductItem.kode_product || "Tidak Ditemukan",
                    deskripsi_product: relatedDiskonProductItem.deskripsi_product || "Tidak Ditemukan",
                    foto_product: relatedDiskonProductItem.foto_product || "Tidak Ditemukan",
                    kategori: relatedDiskonProductItem.kategoris_id || "Tidak Ditemukan",
                    quantity: diskonProduct.target_product_quantity,
                    hb: relatedDiskonProductItem.harga_beli_product || 0,
                    thb: 0,
                    hj: relatedDiskonProductItem.harga_product || 0,
                    tt_b: 0,
                    tt_a: 0,
                    total_pajak: 0,
                    note: diskonProduct.nama_diskon,
                    payment: paymentData.value,
                    rounding: rounding.value,
                    total_after_rounding: totalAfterRounding.value,
                    amount_paid: amountPaid.value,
                    change: change.value,
                    orderID: createorderID(),
                    guest: guest.value || '',
                    orderType: orderType.value,
                    table: tableData.value,
                    is_product_diskon: true,
                    id_product: relatedDiskonProductItem.id || "Tidak Ditemukan",
                });
            });
            console.log("Diskon yang diterapkan:", increaseApplicableDiscountProduct);
            }
        }
    };

    const decreaseExistQty = (item) => {
        if (isConfirmPayment) return;
        const cartItem = cart.value.find(cartItem => cartItem.id_product === item.id_product);
        if (cartItem && cartItem.quantity > 1) {
            cartItem.quantity--;
            cartItem.tt_b = cartItem.hj * cartItem.quantity;
            cartItem.total_pajak = cartItem.tt_b * (props.pajak / 100);
            cartItem.tt_a = cartItem.tt_b + cartItem.total_pajak;
            const decreaseApplicableDiscountProduct = props.diskonThresholdByProduct.filter(
                (diskonProduct) => cartItem.id_product === diskonProduct.product_id && cartItem.quantity < diskonProduct.minimum_items_count
            );
            const decreaseApplicableDiscountOrder = props.diskonThresholdByOrder.filter(
                (diskonOrder) => calculateSubtotal() < diskonOrder.minimum_order_amount
            );
            if (decreaseApplicableDiscountOrder.length > 0) {
                for (let i = cart.value.length - 1; i >= 0; i--) {
                    if (cart.value[i].note && decreaseApplicableDiscountOrder.some(d => cart.value[i].note === d.nama_diskon)) {
                        cart.value.splice(i, 1);
                    }
                }
                console.log(`product diskon diaps`);
            } 
            if (decreaseApplicableDiscountProduct.length > 0) {
                for (let i = cart.value.length - 1; i >= 0; i--) {
                    if (cart.value[i].note && decreaseApplicableDiscountProduct.some(d => cart.value[i].note === d.nama_diskon)) {
                        cart.value.splice(i, 1);
                    }
                }
                console.log(`product diskon diaps`);
            } 
            else {
                console.log(`gaada produk diskon yg diapus`);
            }
        }
    };

    const addToCart = () => {
        if (selectedProduct.value) {
            const totalHargaBeliPerItem = selectedProduct.value.harga_beli_product;
            const totalHargaBeli = totalHargaBeliPerItem * quantity.value;
            const totalHargaPerItem = selectedProduct.value.harga_product;
            const totalHargaBefore = totalHargaPerItem * quantity.value;
            const existingProductIndex = cart.value.findIndex(item => item.kode_product === selectedProduct.value.kode_product);
            const totalPajakPerItem =  totalHargaBefore * (props.pajak / 100);
            const totalHargaAfter = totalHargaBefore + totalPajakPerItem;

            if (existingProductIndex !== -1) {
                alert('produk sudah ada di keranjang')
                return;
            }
            if (!selectedProduct.value) {
                alert('Silakan pilih produk terlebih dahulu.');
                return;
            }

            const applicableDiscountProduct = props.diskonThresholdByProduct.filter((diskonProduct) => selectedProduct.value.id === diskonProduct.product_id && quantity.value >= diskonProduct.minimum_items_count);
            applicableDiscountProduct.forEach((diskonProduct) => {
                const relatedProduct = props.product.find(p => p.id === diskonProduct.target_product_id) || {};
                cart.value.unshift({
                    np: relatedProduct.nama_product || "Tidak Ditemukan",
                    kode_product: relatedProduct.kode_product || "Tidak Ditemukan",
                    deskripsi_product: relatedProduct.deskripsi_product || "Tidak Ditemukan",
                    foto_product: relatedProduct.foto_product || "Tidak Ditemukan",
                    kategori: relatedProduct.kategoris_id || "Tidak Ditemukan",
                    quantity: diskonProduct.target_product_quantity,
                    hb: relatedProduct.harga_beli_product || 0,
                    thb: 0,
                    hj: relatedProduct.harga_product || 0,
                    tt_b: 0,
                    tt_a: 0,
                    total_pajak: 0,
                    note: diskonProduct.nama_diskon,
                    payment: paymentData.value,
                    rounding: rounding.value,
                    total_after_rounding: totalAfterRounding.value,
                    amount_paid: amountPaid.value,
                    change: change.value,
                    orderID: createorderID(),
                    guest: guest.value || '',
                    orderType: orderType.value,
                    table: tableData.value,
                    is_product_diskon: true,
                    id_product: relatedProduct.id || "Tidak Ditemukan",
                });
            });
            console.log("Diskon yang diterapkan:", applicableDiscountProduct);

            cart.value.unshift({
                np: selectedProduct.value.nama_product,
                kode_product: selectedProduct.value.kode_product,
                deskripsi_product: selectedProduct.value.deskripsi_product,
                foto_product: selectedProduct.value.foto_product,
                kategori: selectedProduct.value.kategoris_id,
                quantity: quantity.value,
                hb: totalHargaBeliPerItem,
                thb: totalHargaBeli,
                hj: totalHargaPerItem,
                tt_b: totalHargaBefore,
                tt_a: totalHargaAfter,
                total_pajak: totalPajakPerItem,
                note: note.value || '',
                payment: paymentData.value,
                rounding: rounding.value,
                total_after_rounding: totalAfterRounding.value,
                amount_paid: amountPaid.value,
                change: change.value,
                orderID: createorderID(),
                guest: guest.value || '',
                orderType: orderType.value,
                table: tableData.value,
                is_product_diskon: false,
                id_product: selectedProduct.value.id,
            })
            console.log(cart);
            console.log(cart.value.length);
            isModalOpen.value = false;
            quantity.value = 1;
            runDiskonByOrderValidation();
        }
    }

    const runDiskonByOrderValidation = () => {
        const applicableDiscountOrder = props.diskonThresholdByOrder.filter((diskonOrder) => calculateSubtotal() >= diskonOrder.minimum_order_amount);
        applicableDiscountOrder.forEach((diskonOrder) => {
            const relatedProduct = props.product.find(p => p.id === diskonOrder.target_product_id) || {};
            cart.value.unshift({
                np: relatedProduct.nama_product || "Tidak Ditemukan",
                kode_product: relatedProduct.kode_product || "Tidak Ditemukan",
                deskripsi_product: relatedProduct.deskripsi_product || "Tidak Ditemukan",
                foto_product: relatedProduct.foto_product || "Tidak Ditemukan",
                kategori: relatedProduct.kategoris_id || "Tidak Ditemukan",
                quantity: diskonOrder.target_product_quantity,
                hb: relatedProduct.harga_beli_product || 0,
                thb: 0,
                hj: relatedProduct.harga_product || 0,
                tt_b: 0,
                tt_a: 0,
                total_pajak: 0,
                note: diskonOrder.nama_diskon,
                payment: paymentData.value,
                rounding: rounding.value,
                total_after_rounding: totalAfterRounding.value,
                amount_paid: amountPaid.value,
                change: change.value,
                orderID: createorderID(),
                guest: guest.value || '',
                orderType: orderType.value,
                table: tableData.value,
                is_product_diskon: true,
                id_product: relatedProduct.id || "Tidak Ditemukan",
            });
        });
    }

    const confirmOrder = () => {
        if (cart.value.length === 0) {
            console.log('Keranjang kosong. Silakan tambahkan produk.');
            return;
        }
        if (orderType.value === 'Dine In' && tableData.value === '') {
            console.log('table blm di input');
            isTableModalOpen.value = true;
            return;
        }
        if (guest.value < 1) {
            console.log('guest blm di input');
            isGuestEditModalOpen.value = true;
            return;
        }
        if (paymentData.value === "Payment" || !paymentData.value) {
            console.log('payment blm dipilih');
            isPaymentModalOpen.value = true;
            return;
        }
        router.post('/confirm-order', { cart: cart.value }, {
            onSuccess: () => {
                console.log('Checkout berhasil');
                cart.value = [];
                paymentData.value = "Payment";
                orderType.value = "Dine In";
                orderID.value = '';
                guest.value = 0;
                tableData.value = '';
                isConfirmPayment = false;
            },
            onError: (errors) => {
                console.error("Gagal checkout:", errors);
            }
        });
    }

    const removeFromCart = (index) => {
        if (isCooldown) {      
            console.log('cooldown');
            return;
        }
        if (isConfirmPayment) return;

        if (cart.value.length > 0) {
            isCooldown = true;
            const removedItem = cart.value.splice(index, 1)[0];
            console.log('product dihapus:', removedItem);
            for (let i = cart.value.length - 1; i >= 0; i--) {
                if (
                    cart.value[i].note && 
                    props.diskonThresholdByProduct.some(d => 
                        d.product_id === removedItem.id_product && 
                        cart.value[i].note === d.nama_diskon
                    )
                ) {
                    console.log(`Menghapus diskon terkait: ${cart.value[i].note}`);
                    cart.value.splice(i, 1);
                }
            }

            const totalHargaSebelumDiskonPajak = cart.value.reduce((total, item) => {
                return total + item.tt_b;
            }, 0);

            console.log('Total harga setelah penghapusan:', totalHargaSebelumDiskonPajak);

            setTimeout(() => {
                isCooldown = false;
                console.log('Cooldown selesai, Anda dapat menghapus produk lagi.');
            }, 1000);
        } else {
            console.log('Keranjang kosong, tidak ada yang bisa dihapus.');
        }
    };


    const filteredProducts = computed(() => {
        if (!Array.isArray(props.product)) return [];
        const query = searchQuery.value.toLowerCase();
        return props.product.filter(product => {
            const matchesSearch = product.nama_product.toLowerCase().includes(query);
            if (activeMenu.value === 1337) {
                return matchesSearch;
            }
            const matchesCategory = activeMenu.value ? product.kategoris_id === activeMenu.value : true;
            return matchesSearch && matchesCategory;
        });
    });
    const visibleProducts = computed(() => filteredProducts.value.slice(0, loadedProducts.value));

    const loadMoreProducts = () => {
        loadedProducts.value += productsPerPage;
    };

    const filteredAndSortedTables = computed(() => {
        if (!Array.isArray(props.table)) return [];
        const query = searchQueryTables.value.toLowerCase();
        const filtered = props.table.filter(table => {
            const matchesSearch = table.nomor_meja.toLowerCase().includes(query);
            return matchesSearch;
        });
        return filtered;
    });

    const toggleActive = (categoryId) => {
        activeMenu.value = categoryId;
    };
    const formatCurrency = (value) => {
        if (!value) return "0";
        return Number(value).toLocaleString('id-ID');
    };
    const formattedOrders = computed(() => 
        props.trackOrder
            .map(order => ({
                ...order,
                created_at: order.created_at
                    ? new Date(order.created_at).toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    })
                    : ''
            }))
            .slice()
            .reverse()
    );
    const visibleOrders = computed(() => formattedOrders.value.slice(0, loadedOrders.value));

    const loadMoreOrders = () => {
        loadedOrders.value += ordersPerPage;
    };

    const onScroll = (event) => {
        const { scrollTop, scrollHeight, clientHeight, scrollLeft, scrollWidth, clientWidth } = event.target;
        if (event.target.id === "order-list" && scrollLeft + clientWidth >= scrollWidth - 10) {
            loadMoreOrders();
        }
        if (event.target.id === "product-list" && scrollTop + clientHeight >= scrollHeight - 10) {
            loadMoreProducts();
        }
    };

    const inputFormatCurrency = (event) => {
        let value = event.target.value.replace(/\D/g, "");
        value = Number(value).toLocaleString("id-ID");
        amountPaid.value = value;
    };
    const updateDateTime = () => {
        const now = new Date();
        let hours = now.getHours();
        const minutes = now.getMinutes();
        period.value = hours >= 12 ? "PM" : "AM";

        hours = hours % 12 || 12;
        time.value = `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}`;
        const options = { weekday: "short", day: "2-digit", month: "long", year: "numeric" };
        date.value = now.toLocaleDateString("en-US", options);
    };

    const getProductCount = (categoryId) => {
    return props.product.filter(product => product.kategoris_id === categoryId).length;
    };

    const typeEffect = () => {
        if (!isDeleting && index < placeholderText.length) {
            displayPlaceholder.value += placeholderText[index];
            index++;
        } else if (isDeleting && index > 0) {
            displayPlaceholder.value = displayPlaceholder.value.slice(0, -1);
            index--;
        }
        
        if (index === placeholderText.length) {
            isDeleting = true;
            setTimeout(typeEffect, 400);
        } else if (index === 0) {
            isDeleting = false;
            setTimeout(typeEffect, 100);
        } else {
            setTimeout(typeEffect, isDeleting ? 50 : 100);
        }
    };

    </script>
    <style>
        * {
            .no-select {
                user-select: none;
            }   
        }
        .slide-enter-active, .slide-leave-active {
            transition: transform 0.3s ease-in-out;
        }
        .slide-enter-from, .slide-leave-to {
            transform: translateX(100%);
        }
        .slide-enter-to, .slide-leave-from {
            transform: translateX(0);
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            scrollbar-width: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
        }

    </style>
    <template>
        <FlashMessage class="z-[100]"></FlashMessage>
        <div class="flex flex-row h-screen w-full bg-[#F8F8F8] tracking-tight overflow-y-hidden no-select">
            <div class="flex flex-col h-screen" :class="showCart ? 'w-[76%]' : 'w-full'">
                <div class="flex flex-row w-full px-4 py-4 pb-1 h-auto items-center gap-4 justify-between">
                    <div class="flex gap-4">
                        <div class="hamburger-menu w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-white text-[#2D71F8] cursor-pointer">
                            <i class="ri-menu-5-fill text-current text-xl"></i>
                        </div>
                        <div class="flex w-auto pr-5 h-[3.2rem] rounded-full items-center bg-white">
                            <div class="w-9 h-9 rounded-full mx-2 flex items-center justify-center bg-[#f0f7ff]">
                                <img class="h-6 w-auto" src="../Assets/profileimg.png" alt="">
                            </div>
                            <div class="flex flex-col justify-center mb-1">
                                <div class="font-medium text-gray-700">
                                    {{ props.namaKasir }}
                                </div>
                                <div class="text-xs font-normal text-gray-400">
                                    Cashier
                                </div>
                            </div>
                        </div>
                        <div class="tanggal w-60 h-[3.2rem] rounded-full items-center bg-white flex">
                            <div class="wrap px-2 flex flex-row items-center">
                                <div
                                class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8]">
                                <i class="ri-calendar-line text-current text-lg"></i>
                            </div>
                            <p class="text-gray-700 font-medium ml-3">{{ date }}</p>
                        </div>
                        </div>
                        <div class="jam w-36 h-[3.2rem] rounded-full items-center bg-white flex">
                        <div class="wrap px-2 flex flex-row items-center">
                            <div
                            class="icon w-9 h-9 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8]">
                            <i class="ri-time-line text-current text-lg"></i>
                        </div>
                        <p class="text-gray-700 font-semibold ml-3">{{ time }} <span class="text-gray-400">{{ period }}</span></p>
                    </div>
                        </div>
                    </div>
                <div v-if="!showCart" @click="toggleCart" class="cart-btn-before w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-white text-[#2D71F8] cursor-pointer">
                    <i class="ri-file-list-3-line text-current text-xl"></i>
                </div>
                </div>
                <div class="flex flex-row h-48 w-[100%] px-4 py-4 pb-4 gap-4 overflow-x-auto overflow-y-hidden whitespace-nowrap no-scrollbar">
                    <div @click="toggleActive(1337)" :class="['flex flex-row py-3 px-3 w-48 h-20 rounded-2xl cursor-pointer',activeMenu === 1337 ? 'bg-[#f0f7ff] outline outline-2 outline-[#2D71F8]' : 'bg-white']">
                        <div :class="['category_icon flex justify-center items-center w-14 h-14 rounded-full',activeMenu === 1337 ? 'bg-[#2D71F8] text-white' : 'bg-gray-100 text-gray-500']">
                            <i class="ri-restaurant-2-line text-xl text-current"></i>
                        </div>
                        <div class="wrap flex flex-col ml-3 justify-center">
                            <div class="nama_kategori font-semibold text-lg" :class="[activeMenu === 1337 ? 'text-[#2D71F8]' : 'text-gray-700']">All Menu</div>
                            <div class="total_product_in_kategori text-sm text-gray-500">{{ props.product.length }} items</div>
                        </div>
                    </div>  
                    <div v-for="kategori in kategori" :key="kategori.id" @click="toggleActive(kategori.id)" :class="['flex flex-row py-3 px-3 w-48 h-20 rounded-2xl cursor-pointer',activeMenu === kategori.id ? 'bg-[#f0f7ff] outline outline-2 outline-[#2D71F8]' : 'bg-white']">
                        <div :class="['category_icon flex justify-center items-center w-14 h-14 rounded-full',activeMenu === kategori.id ? 'bg-[#2D71F8] text-white' : 'bg-gray-100 text-gray-500']">
                            <i class="text-xl text-current" :class="[kategori.icon]"></i>
                        </div>
                        <div class="wrap flex flex-col ml-3 justify-center">
                            <div class="nama_kategori font-semibold text-lg" :class="[activeMenu === kategori.id ? 'text-[#2D71F8]' : 'text-gray-700']">{{ kategori.nama_kategori}}</div>
                            <div class="total_product_in_kategori text-sm text-gray-500">{{ getProductCount(kategori.id) }} items</div>
                        </div>
                    </div>
                </div>
                <!-- search -->
                <div class="flex items-center justify-center w-[97%] h-auto px-1 pr-1 bg-white rounded-full py-1 mx-auto">
                    <div class="icon flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full shrink-0 text-gray-700">
                        <i class="bi bi-search text-lg text-current"></i>
                    </div>
                    <input v-model="searchQuery" type="text" class="flex-1 bg-transparent px-4 placeholder:text-gray-400 text-gray-700 font-semibold border-none focus:outline-none focus:ring-0" :placeholder="displayPlaceholder"/>
                </div>
                <div id="product-list" class="flex flex-wrap justify-left mx-auto flex-row gap-[1.20rem] w-full px-4 mt-5 h-full pb-[15%] overflow-auto">
                    <div v-for="product in visibleProducts":key="product.id" @click="openModal(product)"class="flex flex-col px-3 py-3 bg-white w-52 h-64 rounded-xl overflow-hidden cursor-pointer">
                    <div class="img rounded-xl w-full h-[65%] bg-[#F6F6F6] flex justify-center items-center">
                        <img class="max-h-32 w-auto object-contain":src="'http://127.0.0.1:8000/storage/' + product.foto_product"alt=""/>
                    </div>
                    <div class="nama_product text-medium font-semibold text-gray-700 pt-2 text-left">
                        {{ product.nama_product }}
                    </div>
                    <div class="flex flex-row w-full justify-between items-center mt-auto">
                        <div class="kategori flex items-center justify-center px-2 py-0.5 rounded-xl w-fit text-xs font-semibold":style="{ backgroundColor: product.kategori?.warna_background_kategori, color: product.kategori?.warna_teks_kategori }">
                        {{ product.kategori?.nama_kategori }}
                        </div>
                        <div class="harga text-gray-700 font-semibold text-lg">
                        Rp{{ formatCurrency(product.harga_product) }}
                        </div>
                    </div>
                    </div>
                    <div v-if="visibleProducts.length === 0" class="flex w-full h-64 rounded-xl overflow-hidden justify-center">
                    <h2 class="text-xl text-gray-400">No Product Available</h2>
                    </div>
                </div>
                <!-- track order -->
                <div class="fixed tracking-normal left-0 w-full h-24 bg-white shadow-[0px_-10px_20px_2px_rgba(193,195,199,0.2)] flex items-center justify-between px-4 transition-all duration-300 ease-in-out" :class="{ '-bottom-24': !isTrackOrderOpen, 'bottom-0': isTrackOrderOpen }">
                    <div id="order-list" class="no-scrollbar flex flex-row w-[76%] justify-start items-center gap-3 h-24 overflow-x-auto whitespace-nowrap">
                        <div v-for="order in visibleOrders":key="order.order_id"class="cursor-pointer flex flex-col border-2 rounded-2xl h-[4.7rem] w-64 py-1 px-3 justify-center"@click="openTrackOrderModal(order)">
                            <div class="flex flex-row justify-between mb-1">
                                <div class="text-md text-gray-800">#{{ order.order_id }}</div>
                                <div class="flex justify-center items-center bg-[#ebfff5] text-[0.67rem] text-[#1C8370] rounded-full py-[0.100rem] px-[0.590rem]">All Done</div>
                            </div>
                            <div class="flex flex-row justify-between">
                                <div class="text-sm text-gray-400 mr-4">{{ order.guest }} Guests • {{ order.order_type }}</div>
                                <div class="flex justify-center items-center text-sm text-gray-400">{{ order.created_at }}</div>
                            </div>
                        </div>
                        <div v-if="visibleOrders.length === 0" class="flex pl-3 text-gray-400">No Order Data Available Today</div>
                    </div>
                    <div v-if="isTrackOrderOpen" @click=(toggleTrackOrder) class="cursor-pointer absolute -top-[3.24rem] left-4 bg-white p-7 py-4 rounded-t-2xl shadow-[0px_-10px_20px_2px_rgba(193,195,199,0.2)] ">
                        <div class="flex flex-row w-full justify-between items-center">
                            <div class="text-[#2D71F8] text-base">
                                Track Order
                            </div>
                            <div class="wrap px-2 flex flex-row items-center">
                                <div class="icon w-6 h-6 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8]">
                                    <i class="bi bi-dash text-current text-sm"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else @click="(toggleTrackOrder)" class="cursor-pointer absolute -top-[2.976rem] left-4 bg-[#2D71F8] p-7 py-3 rounded-t-2xl shadow-[0px_-10px_20px_2px_rgba(193,195,199,0.2)] ">
                        <div class="flex flex-row w-full justify-between items-center">
                            <div class="text-white text-base">
                                Track Order
                            </div>
                            <div class="wrap px-2 flex flex-row items-center">
                                <div class="icon w-6 h-6 bg-[#f0f7ff] rounded-full flex items-center justify-center text-[#2D71F8]"> 
                                    <i class="bi bi-plus text-current text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- track order Modal -->
                <div v-if="isTrackOrderModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50"@click.self="closeTrackOrderModal">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[11rem] -translate-y-1/2 bg-white rounded-2xl w-[35rem] shadow-2xl">
                        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <div class="flex w-9 h-9 bg-white"></div>
                                <div class="flex flex-col justify-center items-center">
                                    <h2 class="text-normal font-normal">Track Order</h2>
                                </div>
                                <div class="flex items-center justify-center rounded-full bg-[#fff2f3] w-9 h-9 text-[#FC4A4A] cursor-pointer" @click="closeTrackOrderModal">
                                    <i class="bi bi-x-lg text-current"></i>
                                </div>
                            </div>
                            <div class="flex w-full flex-col max-h-[34rem] overflow-auto mb-5">
                                <div class="flex flex-col w-full h-50 py-4 mb-1 px-5 justify-between">
                                    <div class="flex flex-row justify-left items-center w-full h-auto mb-2">
                                    <div class="text-xl italic font-medium mr-11 text-gray-800">#{{ selectedOrder?.order_id }}</div>
                                    <div class="flex justify-center items-center bg-[#ebfff5] text-sm text-[#1C8370] rounded-full py-[0.100rem] px-4">All Done</div>
                                </div>
                                <div class="flex flex-row w-auto pb-4 border-b-2 border-dashed text-gray-500">
                                    <div class="flex flex-col mr-8 w-auto">
                                        <div class="text-[0.920rem]">Table: {{ selectedOrder?.tables }}</div>
                                        <div class="text-[0.920rem]">Guest: {{ selectedOrder?.guest }} Guests</div>
                                    </div>
                                    <div class="flex flex-col mr-8 w-auto">
                                        <div class="text-[0.920rem]">Order type: {{ selectedOrder?.order_type }}</div>
                                        <div class="text-[0.920rem]">Cashier: {{ selectedOrder?.nama_kasir }}</div>
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="text-[0.920rem]">Date Order:</div>
                                        <div class="flex justify-center items-start text-[0.920rem]">{{ formattedDataOrderUpdatedAt }}</div>
                                    </div>
                                </div>
                                <div class="flex flex-col justify-start items-start text-gray-500 w-full h-auto pt-3 pb-4 border-dashed border-b-2">
                                    <div class="text-[0.920rem]">Total Price: Rp{{ formatCurrency(selectedOrder?.total_harga_after_all) }}</div>
                                    <div class="text-[0.920rem]">Rounding: {{ selectedOrder?.rounding }}</div>
                                    <div class="text-[0.920rem]">Amount Paid: Rp{{ formatCurrency(selectedOrder?.amount_paid) }}</div>
                                    <div class="text-[0.920rem]">Change: Rp{{ formatCurrency(selectedOrder?.change) }}</div>
                                </div>
                            </div>
                            <div class="flex w-full h-auto flex-col">
                                <div class="flex flex-col max-h-40 w-full mb-4 pb-4 overflow-y-auto shadow-lg shadow-gray-100 rounded-md">
                                    <div v-for="product in selectedProducts" :key="product.id" class="text-[0.940rem] text-gray-500 px-5 mb-2">
                                        <span class="mr-3">{{ product.quantity }}x</span>{{ product.nama_product }}
                                    </div>
                                </div>
                                <div class="px-5 text-[0.940rem] text-gray-500 flex items-center">
                                    <span class="mr-3">Total Order:</span>{{ selectedProducts.length }} Items
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- product Modal -->
                <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50" @click.self="closeModal">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <div class="flex w-9 h-9 bg-white"></div>
                            <h2 class="text-normal font-normal">Detail Produk</h2>
                            <div class="flex items-center justify-center rounded-full bg-[#fff2f3] w-9 h-9 text-[#FC4A4A] cursor-pointer" @click="closeModal">
                                <i class="bi bi-x-lg text-current"></i>
                            </div>
                        </div>
                        <div v-if="selectedProduct">
                            <div class="mt-3 img rounded-2xl w-[95%] m-auto h-44 bg-[#F5F5F5] flex justify-center items-center">
                                <img class="max-h-32 w-auto object-contain" :src="'http://127.0.0.1:8000/storage/' + selectedProduct.foto_product" alt="{{ selectedProduct.kode_product }} {{ selectedProduct.deskripsi_product }} {{ selectedProduct.id }} {{ selectedProduct.harga_beli_product }}">
                            </div>
                            <div class="flex flex-col mt-1 p-3">
                                <div class="kategori flex items-center justify-center px-2 py-0.5 rounded-xl w-fit text-xs font-semibold" :style="{ backgroundColor: selectedProduct.kategori?.warna_background_kategori, color: selectedProduct.kategori?.warna_teks_kategori}"> {{selectedProduct.kategori?.nama_kategori}}
                                </div>
                                <p class="mt-2 text-gray-700 font-semibold text-lg">{{ selectedProduct.nama_product }}</p>
                                <p class="text-gray-500 text-sm">{{ selectedProduct.deskripsi_product }}</p>
                                <p class="text-[#2D71F8] font-medium text-xl mt-2">Rp{{ formatCurrency(selectedProduct.harga_product) }}</p>
                                <textarea v-model="note" class="flex justify-start items-start mt-4 w-full h-16 bg-[#F5F5F5] text-gray-700 text-xs border-none focus:outline-none focus:ring-0 rounded-2xl p-2 text-left align-top placeholder:text-left" placeholder="Add notes to your order..." type="text" name="" id=""></textarea>
                            </div>
                            <div class="flex mt-1 w-full px-3 rounded-md h-auto shadow-[0px_-4px_6px_rgba(0,0,0,0.1)] shadow-gray-100">
                                <div class="mt-3 quantity w-full h-12 rounded-full bg-[#F5F5F5] flex flex-row justify-between items-center px-1">
                                    <div @click="decreaseQty" class="flex items-center justify-center bg-white w-10 h-10 rounded-full cursor-pointer">
                                        <i class="bi bi-dash"></i>
                                    </div>
                                    <div class="text-xl">{{ quantity }}</div>
                                    <div @click="increaseQty" class="flex items-center justify-center bg-white w-10 h-10 rounded-full cursor-pointer">
                                        <i class="bi bi-plus-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-3">
                            <button @click="addToCart" class="bg-[#2D71F8] w-full text-white px-4 py-4 rounded-b-2xl hover:bg-[#6196ff]">Add to Cart</button>
                        </div>
                    </div>
                </div>
                <!-- Cart note Modal -->
                <div v-if="isCartNoteModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50" @click.self="closeCartNoteModal">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <div class="flex w-9 h-9 bg-white"></div>
                            <h2 class="text-normal font-normal"> Item Notes</h2>
                            <div class="flex items-center justify-center rounded-full bg-[#fff2f3] w-9 h-9 text-[#FC4A4A] cursor-pointer" @click="closeCartNoteModal">
                                <i class="bi bi-x-lg text-current"></i>
                            </div>
                        </div>
                        <div v-if="selectedCartProduct" >
                            <div class="mt-3 img rounded-2xl w-[95%] m-auto h-44 bg-[#F5F5F5] flex justify-center items-center">
                                <img class="max-h-32 w-auto object-contain" :src="'http://127.0.0.1:8000/storage/' + selectedCartProduct.foto_product" alt="{{ selectedCartProduct.kode_product }}">
                            </div>
                            <div class="flex flex-col mt-1 p-3">
                                <p class="mt-2 text-gray-700 font-semibold text-lg">{{ selectedCartProduct.np }}</p>
                                <p class="text-gray-500 text-sm">{{ selectedCartProduct.deskripsi_product }}</p>
                                <textarea v-model="note" class="flex justify-start items-start mt-4 w-full h-16 bg-[#F5F5F5] text-gray-700 text-xs border-none focus:outline-none focus:ring-0 rounded-2xl p-2 text-left align-top placeholder:text-left" type="text" name="" id="" :placeholder="selectedCartProduct.note.length === 0 ? 'Add notes to your order...' : ''"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end mt-3">
                            <button @click="saveNote" class="bg-[#2D71F8] w-full text-white px-4 py-4 rounded-b-2xl hover:bg-[#6196ff]">Save Note</button>
                        </div>
                    </div>
                </div>
                <!-- Payment Modal -->
                <div v-if="isPaymentModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50" @click.self="closePaymentModal">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <div class="flex w-9 h-9 bg-white"></div>
                            <h2 class="text-normal font-normal">Payment</h2>
                            <div class="flex items-center justify-center rounded-full bg-[#fff2f3] w-9 h-9 text-[#FC4A4A] cursor-pointer" @click="closePaymentModal">
                                <i class="bi bi-x-lg text-current"></i>
                            </div>
                        </div>
                        <div class="flex w-full flex-col px-3 max-h-96 overflow-auto mb-5">
                            <div v-for="payment in payment" :key="payment.id">
                                <div @click="confirmPayment(payment)" class="cursor-pointer flex items-center justify-center w-full h-16 mt-4 bg-gray-100 rounded-2xl font-semibold">{{ payment.payment_name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Amount Paid Modal -->
                <div v-if="isAmountPaidModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-center items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <h2 class="text-normal h-9 flex items-center justify-center font-normal">Amount Paid</h2>
                        </div>
                        <input v-model="amountPaid" @input='inputFormatCurrency' class="flex justify-center items-center text-center mt-4 w-[85%] mx-auto h-16 bg-[#F5F5F5] text-gray-700 text-md border-none focus:outline-none focus:ring-0 rounded-2xl py-2 appearance-none" type="text" name="" id="" placeholder="Input Customer Amount Paid">
                        <div class="flex justify-center items-center mt-3">
                            <button @click="storeAmountPaid" class="bg-[#2D71F8] w-full text-white px-4 py-4 rounded-b-2xl hover:bg-[#6196ff]">Confirm</button>
                        </div>
                    </div>
                </div>
                <!-- Kembalien Modal -->
                <div v-if="isChangeModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-center items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <h2 class="text-normal font-normal">Change</h2>
                        </div>
                        <div class="flex justify-center items-center mt-4 w-[85%] mx-auto h-16 bg-[#F5F5F5] text-gray-700 text-md rounded-2xl">
                            Rp {{ formatCurrency(change) }}
                        </div>
                        <div class="flex justify-center items-center mt-3">
                            <button @click="closeChangeModal" class="bg-[#2D71F8] w-full text-white px-4 py-4 rounded-b-2xl hover:bg-[#6196ff]">Confirm</button>
                        </div>
                    </div>
                </div>
                <!-- guest edit modal -->
                <div v-if="isGuestEditModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50" @click.self(isGuestEditModalOpen)>
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[26rem] shadow-2xl">
                        <div class="flex flex-row justify-center items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <h2 class="text-normal h-9 flex items-center justify-center font-normal">Edit Guest</h2>
                        </div>
                        <input v-if="guest.length > 0" v-model="guest"class="flex justify-center items-center text-center mt-4 w-[85%] mx-auto h-16 bg-[#F5F5F5] text-gray-700 text-md border-none focus:outline-none focus:ring-0 rounded-2xl py-2 appearance-none" type="text" name="" id="" placeholder="JANCUK">
                        <input v-else @input="guest = $event.target.value"  class="flex justify-center items-center text-center mt-4 w-[85%] mx-auto h-16 bg-[#F5F5F5] text-gray-700 text-md border-none focus:outline-none focus:ring-0 rounded-2xl py-2 appearance-none" type="text" name="" id="" placeholder="Input Guest Count">
                        <div class="flex justify-center items-center mt-3">
                            <button @click="storeGuest" class="bg-[#2D71F8] w-full text-white px-4 py-4 rounded-b-2xl hover:bg-[#6196ff]">Confirm</button>
                        </div>
                    </div>
                </div>
                <!-- Check Discount Modal -->
                <div v-if="isCheckDiscountModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50" @click.self="closeCheckDiscountModal">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[12rem] -translate-y-1/2 bg-white rounded-2xl w-[37rem] shadow-2xl">
                        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <div class="flex w-9 h-9 bg-white"></div>
                            <div class="flex flex-col justify-center items-center">
                                <h2 class="text-normal font-normal">Check Discount</h2>
                                <h2 class="text-normal font-normal text-gray-500">Discounts will used automatically</h2>
                            </div>
                            <div class="flex items-center justify-center rounded-full bg-[#fff2f3] w-9 h-9 text-[#FC4A4A] cursor-pointer" @click="closeCheckDiscountModal">
                                <i class="bi bi-x-lg text-current"></i>
                            </div>
                        </div>
                        <div class="flex w-full flex-col px-3 max-h-[34rem] overflow-auto mb-5">
                            <div class="flex flex-row items-center justify-between mt-4 w-44 mx-auto text-gray-500">
                                    <div class="flex flex-row">
                                        <div class="flex items-center justify-center p-1 rounded-full bg-rose-200 mr-2">
                                            <div class="bg-[#FC4A4A] p-2 rounded-full"></div>
                                        </div>
                                        Not used
                                    </div>
                                    <div class="flex flex-row">
                                        <div class="flex items-center justify-center p-1 rounded-full bg-[#d4ffea] mr-2">
                                            <div class="bg-[#1C8370] p-2 rounded-full"></div>
                                        </div>
                                        Used
                                    </div>
                                </div>
                            <div v-for="diskon in allActiveDiscounts" :key="diskon.id">
                                <div class="flex flex-row w-full h-auto mt-4 rounded-2xl bg-gray-100 justify-start items-center">
                                    <div class="flex w-[25%] h-auto items-center justify-center border-r-2 border-gray-200">
                                        <div class="flex items-center justify-center rounded-full p-2"
                                            :class="{
                                                'bg-[#d4ffea]': usedDiscounts.some(d => d.nama_diskon === diskon.nama_diskon), 
                                                'bg-rose-200': !usedDiscounts.some(d => d.nama_diskon === diskon.nama_diskon)
                                            }">
                                            <div class="p-4 rounded-full" 
                                                :class="{
                                                    'bg-[#1C8370]': usedDiscounts.some(d => d.nama_diskon === diskon.nama_diskon), 
                                                    'bg-[#FC4A4A]': !usedDiscounts.some(d => d.nama_diskon === diskon.nama_diskon)
                                                }">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex w-full h-full flex-col px-5 items-start justify-center py-7">
                                        <div class="text-gray-600 text-[1.14rem] font-medium">{{ diskon.nama_diskon }}</div>
                                        <div class="text-[#2D71F8] text-[0.87rem] font-medium">{{ diskon.nama_kategori_diskon }} <span class="text-gray-500">|| {{ diskon.stok_diskon }} Tersisa </span> </div>
                                        <div class="text-gray-500 text-[1rem] font-medium">{{ diskon.deskripsi_diskon }}</div>
                                    </div>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
                <!-- meja Modal -->
                <div v-if="isTableModalOpen" class="fixed inset-0 flex items-center justify-center bg-slate-400 bg-opacity-50" @click.self="closeTableModal">
                    <div class="absolute top-1/2 left-1/3 transform -translate-x-[7rem] -translate-y-1/2 bg-white rounded-2xl w-[31rem] shadow-2xl">
                        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
                            <div class="flex w-9 h-9 bg-white"></div>
                            <h2 class="text-normal font-normal">Tables</h2>
                            <div class="flex items-center justify-center rounded-full bg-[#fff2f3] w-9 h-9 text-[#FC4A4A] cursor-pointer" @click="closeTableModal">
                                <i class="bi bi-x-lg text-current"></i>
                            </div>
                        </div>
                        <div class="flex w-full flex-col px-3 max-h-[30rem] overflow-auto mb-5">
                            <div class="flex items-center justify-center w-full px-4 pr-1 bg-white rounded-full h-14 mt-4 py-2 mx-auto border">
                                <input v-model="searchQueryTables" type="text" class="flex-1 bg-transparent px-4 placeholder:text-gray-400 text-gray-700 font-semibold border-none focus:outline-none focus:ring-0" placeholder="Search Table number here."/>
                                    <div class="icon flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full shrink-0 text-gray-700">
                                    <i class="bi bi-search text-lg text-current"></i>
                                </div>
                            </div>
                            <div v-for="tables in filteredAndSortedTables" :key="tables.id">
                                <div @click="(confirmTable(tables))" class="cursor-pointer flex items-center justify-center w-full h-16 mt-4 bg-gray-100 rounded-2xl font-semibold">{{ tables.nomor_meja }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <transition name="slide">
                <div v-if="showCart" class="cart flex flex-col  w-[32.6%] h-screen bg-white shadow-2xl shadow-slate-100 z-50">
                    <div class="flex flex-row h-auto w-full px-3 pt-3 pb-3 justify-between items-center shadow-lg shadow-gray-100">
                        <div v-if="showCart" @click="toggleCart" class="cart-btn-before w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-gray-100 text-slate-700 cursor-pointer">
                            <i class="ri-file-list-3-line text-current text-xl"></i>
                        </div>
                        <div class="flex flex-col text-center items-center">
                            <div class="text-lg font-semibold text-slate-700">{{ guest }} Guest</div>
                            <div class="text-sm text-gray-400"> Order ID: {{ cart.length > 0 ? orderID : ''}}</div>
                        </div>
                        <div @click="toggleGuest" class="cart-btn-before w-[3.2rem] h-[3.2rem] flex items-center justify-center rounded-full bg-gray-100 text-slate-700 cursor-pointer">
                            <i class="ri-pencil-line text-current text-xl"></i>
                        </div>
                    </div>
                    <div class="flex flex-row h-auto w-full px-3 pt-2 pb-3 justify-between items-center shadow-lg shadow-gray-100">
                        <div v-if="isTableActive" @click="(openTableModal)" class="flex flex-row w-[48.5%] font-[500] text-slate-800 bg-gray-100 cursor-pointer items-center justify-between rounded-full px-4 py-3">
                            <div class="text-sm w-auto">Table {{ tableData.length > 0 ? tableData : '- - - -' }}</div>
                            <div class="icons flex items-center justify-center w-5 h-5 rounded-full bg-white">
                                <i class="ri-arrow-left-s-fill"></i>
                            </div>
                        </div>
                        <div v-else class="flex flex-row w-[48.5%] font-[500] text-slate-400 bg-gray-100 items-center justify-between rounded-full px-4 py-3">
                            <div class="text-sm w-auto">Table - - - -</div>
                        </div>
                        <div @click=(getOrderType) class="flex flex-row w-[48.5%] font-[500] text-slate-800 bg-gray-100 cursor-pointer items-center justify-between rounded-full px-4 py-3">
                            <div class="text-sm w-auto">{{ orderType }}</div>
                            <div class="icons flex items-center justify-center w-5 h-5 rounded-full bg-white">
                                <i class="ri-loop-right-fill"></i>
                            </div>
                        </div>
                    </div>
                    <div class="h-full overflow-auto">
                        <div v-if="cart.length > 0" v-for="(item, index) in cart" :key="index" class="flex flex-row w-full h-28 px-3 border-dashed" :class="{ 'border-t-2': index !== 0, 'relative before:block before:content-[\' \'] before:absolute before:top-0 before:right-0 before:bottom-0 before:w-40 before:bg-gradient-to-l before:from-green-500/30 before:to-transparent shadow-inner': allActiveDiscounts.some(diskon => item.note === diskon.nama_diskon)}">
                        <div class="mt-3 img rounded-xl w-[7.1rem] h-[5.2rem] bg-[#F5F5F5] flex justify-center items-center">
                            <img class="max-h-12 w-auto object-contain" :src="'http://127.0.0.1:8000/storage/' + item.foto_product">
                        </div>
                        <div class="mid flex flex-col w-full mt-3 pl-3 h-auto items-start">
                            <div class="text-sm text-slate-800 font-medium">{{ item.np }} x {{ item.quantity }}</div>
                            <div v-for="diskon in allActiveDiscounts" :key="diskon.nama_diskon">
                                <div v-if="item.note === diskon.nama_diskon" class="text-sm text-[#1C8370] font-medium">
                                    {{ diskon.nama_diskon }}
                                </div>
                            </div>

                            <div v-if="!allActiveDiscounts.some(d => d.nama_diskon === item.note)" class="text-sm text-[#2D71F8] font-medium">
                                Rp{{ formatCurrency(item.tt_b) }}
                            </div>
                            <div class="flex flex-row mt-2 w-full justify-between">
                                <div class="flex flex-row">
                                    <div v-for="diskon in allActiveDiscounts" :key="diskon.nama_diskon">
                                        <div v-if="item.note === diskon.nama_diskon"class="flex items-center justify-center bg-[#d4ffea] w-9 h-9 rounded-full">
                                            <div class="flex items-center justify-center bg-[#1C8370] w-7 h-7 rounded-full text-white">
                                                <i class="ri-discount-percent-line text-sm text-current flex items-center justify-center"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="item.note.length > 0 && !allActiveDiscounts.some(diskon => item.note === diskon.nama_diskon)"class="flex items-center justify-center bg-[#bad1ff] w-9 h-9 rounded-full cursor-pointer">
                                        <div @click="openCartNoteModal(item)" class="flex items-center justify-center bg-[#2D71F8] w-7 h-7 rounded-full text-white">
                                            <i class="ri-pencil-line text-current text-sm text-current"></i>
                                        </div>
                                    </div>
                                    <div v-else-if="!allActiveDiscounts.some(diskon => item.note === diskon.nama_diskon)" @click="openCartNoteModal(item)"class="flex items-center justify-center bg-gray-100 w-9 h-9 rounded-full cursor-pointer">
                                        <div class="flex items-center justify-center bg-white w-7 h-7 rounded-full text-gray-700">
                                            <i class="ri-pencil-line text-current text-sm text-current"></i>
                                        </div>
                                    </div>
                                    <div v-if="!allActiveDiscounts.some(diskon => item.note === diskon.nama_diskon)" @click="removeFromCart(index)"class="ml-2 flex items-center justify-center bg-rose-200 w-9 h-9 rounded-full cursor-pointer">
                                        <div class="flex items-center justify-center bg-[#FC4A4A] w-7 h-7 rounded-full text-white">
                                            <i class="ri-delete-bin-line text-current text-sm text-current"></i>
                                        </div>
                                    </div>
                                    <div v-if="allActiveDiscounts.some(diskon => item.note === diskon.nama_diskon)" class="ml-2 quantity w-9 h-9 rounded-full bg-[#F5F5F5] flex flex-row justify-center items-center px-1">
                                        <div class="text-sm flex items-center justify-center bg-white w-7 h-7 rounded-full">
                                            {{ item.quantity }}
                                        </div>
                                    </div>
                                </div>
                                <div v-if="!allActiveDiscounts.some(diskon => item.note === diskon.nama_diskon)"
                                    class="quantity w-[40%] h-9 rounded-full bg-[#F5F5F5] flex flex-row justify-between items-center px-1">
                                    <div @click="decreaseExistQty(item)"
                                        class="flex items-center justify-center bg-white w-7 h-7 rounded-full cursor-pointer">
                                        <i class="bi bi-dash text-xs"></i>
                                    </div>
                                    <div class="text-sm">{{ item.quantity }}</div>
                                    <div @click="increaseExistQty(item)"
                                        class="flex items-center justify-center bg-white w-7 h-7 rounded-full cursor-pointer">
                                        <i class="bi bi-plus-lg text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="flex flex-row w-full items-center justify-center h-28 px-3 items border-b-2 border-dashed">
                        <div class="text-sm text-gray-400">No Item Selected</div>
                    </div>

                    </div>
                    
                    <div class="w-full h-auto rounded-t-3xl shadow-[0px_-10px_20px_rgba(0,0,0,0.1)] shadow-slate-100">
                        <div class="w-full flex flex-col items-start px-3 pt-5 gap-1 pb-2 border-b-2 border-dashed">
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm text-slate-700">Subtotal</div>
                                <div class="text-sm text-slate-700">Rp {{formatCurrency(calculateSubtotal())}}</div>
                            </div>
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm text-slate-400">Tax ({{ props.pajak }}%)</div>
                                <div class="text-sm text-slate-400">Rp {{ formatCurrency(calculateTotalPajak())}}</div>
                            </div>
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm text-slate-400">Rounding</div>
                                <div class="text-sm text-slate-400">{{ roundingAmount() }}</div>
                            </div>
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-sm font-medium text-[#1C8370]">Discount</div>
                                <div class="text-sm font-medium text-[#1C8370]">-Rp 0</div>
                            </div>
                        </div>
                        <div class="w-full flex-col items-start px-3 pb-2 pt-1 shadow-sm shadow-gray-100">
                            <div class="flex flex-row w-full justify-between items-center">
                                <div class="text-lg text-slate-800">Total</div>
                                <div class="text-lg text-slate-800">Rp {{formatCurrency(totalRounding())}}</div>
                            </div>
                        </div>
                        <div class="flex flex-row h-auto w-full px-3 pt-3 pb-3 justify-between items-center">
                            <div @click="openCheckDiscountModal" class="flex flex-row w-[48.5%] font-[500] cursor-pointer items-center justify-between rounded-full pl-4 pr-1 py-1" :class="isAnyDiscountApplied ? 'text-[#1C8370] border border-[#1C8370] bg-[#f7fffc]' : 'text-gray-400 bg-[#F6F6F6]'">
                            <div class="text-sm font-normal w-auto pr-1">
                                {{ isAnyDiscountApplied ? 'Discount applied' : 'Check Discount' }}
                            </div>
                            <div class="icons flex items-center justify-center w-8 h-8 rounded-full" :class="isAnyDiscountApplied ? 'text-white bg-[#1C8370]' : 'text-slate-700 bg-white'">
                                <i class="ri-discount-percent-line text-current text-xl"></i>
                            </div>
                        </div>

                        <div @click="(openPaymentModal)" v-if="!isConfirmPayment" class="flex flex-row w-[48.5%] font-[500] text-black bg-white border border-slate-700 cursor-pointer items-center justify-between rounded-full pl-4 pr-1 py-1">
                            <div class="text-sm font-normal w-auto">{{ paymentData }}</div>
                            <div class="icons flex items-center justify-center w-8 h-8 rounded-full text-slate-700 bg-[#F6F6F6]">
                                <i class="ri-bank-card-line text-current text-xl"></i>
                            </div>
                        </div>
                        <div v-if="isConfirmPayment" class="flex flex-row w-[48.5%] font-[500] text-[#2D71F8] bg-[#f5f8ff] border border-[#2D71F8] cursor-pointer items-center justify-between rounded-full pl-4 pr-1 py-1">
                            <div class="text-sm font-normal w-auto">{{ paymentData }}</div>
                            <div class="icons flex items-center justify-center w-8 h-8 rounded-full text-white bg-[#2D71F8]">
                                <i class="ri-bank-card-line text-current text-xl"></i>
                            </div>
                        </div>
                        </div>
                        <button @click="(confirmOrder)" class="bg-[#2D71F8] w-full h-[3.7rem] text-white text-center px-4 py-4 hover:bg-[#6196ff]">Confirm Order</button>
                    </div>
                </div>
            </transition>
        </div>
    </template>
