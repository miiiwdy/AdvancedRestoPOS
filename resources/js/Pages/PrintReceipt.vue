<template>
    <PrintLayout
      v-if="printData"
      :order-id="printData.orderId"
      :table="printData.table"
      :guest="printData.guest"
      :order-type="printData.orderType"
      :cashier-name="printData.cashierName"
      :current-date="printData.currentDate"
      :total-price="printData.totalPrice"
      :rounding="printData.rounding"
      :amount-paid="printData.amountPaid"
      :change="printData.change"
      :payment-method="printData.paymentMethod"
      :products="printData.products"
    />
  </template>
  
  <script setup>
  import { onMounted, ref } from 'vue';
  import PrintLayout from '@/Components/PrintLayout.vue';
  
  const printData = ref(null);
  
  onMounted(() => {
    const data = localStorage.getItem('printData');
    if (data) {
      printData.value = JSON.parse(data);
      localStorage.removeItem('printData');
      setTimeout(() => {
        window.print();
        window.close();
      }, 500);
    }
  });
  </script>