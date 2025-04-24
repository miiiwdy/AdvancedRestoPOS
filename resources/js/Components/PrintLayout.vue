<template>
    <div class="print-container hidden print:block">
      <div class="flex flex-col bg-white rounded-2xl w-full">
        <div class="flex flex-row justify-between items-center justify-center shadow-lg shadow-gray-100 rounded-md p-3 pb-3">
          <div class="flex w-9 h-9 bg-white"></div>
          <div class="flex flex-col justify-center items-center">
            <h2 class="text-normal font-normal">Order Receipt</h2>
          </div>
          <div class="flex w-9 h-9"></div>
        </div>
        <div class="flex w-full flex-col">
          <div class="flex flex-col w-full h-50 py-4 mb-1 px-5 justify-between">
            <div class="flex flex-row justify-left items-center w-full h-auto mb-2">
              <div class="text-xl italic font-medium mr-11 text-gray-800">#{{ orderId }}</div>
              <div class="flex justify-center items-center bg-[#f2fff9] text-sm text-[#1C8370] rounded-full py-[0.100rem] px-4">All Done</div>
            </div>
            <div class="flex flex-row w-auto pb-4 border-b-2 border-dashed text-gray-500">
              <div class="flex flex-col mr-8 w-auto">
                <div class="text-[0.920rem]">Table: {{ table }}</div>
                <div class="text-[0.920rem]">Guest: {{ guest }} Guests</div>
              </div>
              <div class="flex flex-col mr-8 w-auto">
                <div class="text-[0.920rem]">Order type: {{ orderType }}</div>
                <div class="text-[0.920rem]">Cashier: {{ cashierName }}</div>
              </div>
              <div class="flex flex-col">
                <div class="text-[0.920rem]">Date Order:</div>
                <div class="flex justify-center items-start text-[0.920rem]">{{ currentDate }}</div>
              </div>
            </div>
            <div class="flex flex-col justify-start items-start text-gray-500 w-full h-auto pt-3 pb-4 border-dashed border-b-2">
              <div class="text-[0.920rem]">Total Price: Rp{{ formatCurrency(totalPrice) }}</div>
              <div class="text-[0.920rem]">Rounding: {{ rounding }}</div>
              <div class="text-[0.920rem]">Amount Paid: Rp{{ amountPaid }}</div>
              <div class="text-[0.920rem]">Change: Rp{{ change }}</div>
              <div class="text-[0.920rem]">Payment: {{ paymentMethod }}</div>
            </div>
          </div>
          <div class="flex w-full h-auto flex-col">
            <div class="flex flex-col max-h-40 w-full mb-4 pb-4 overflow-y-auto shadow-lg shadow-gray-100 rounded-md">
              <div v-for="(product, index) in products" :key="index" class="text-[0.940rem] text-gray-500 px-5 mb-2">
                <span class="mr-3">{{ product.quantity }}x</span>{{ product.np }}
              </div>
            </div>
            <div class="px-5 text-[0.940rem] text-gray-500 flex items-center">
              <span class="mr-3">Total Order:</span>{{ products.length }} Items
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  
  const props = defineProps({
    orderId: String,
    table: String,
    guest: Number,
    orderType: String,
    cashierName: String,
    currentDate: String,
    totalPrice: Number,
    rounding: Number,
    amountPaid: Number,
    change: Number,
    paymentMethod: String,
    products: Array,
  });
  
  const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID').format(value);
  };
  </script>
  
  <style>
  @media print {
    body * {
      visibility: hidden;
    }
    .print-container, .print-container * {
      visibility: visible;
    }
    .print-container {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
    }
  }
  </style>