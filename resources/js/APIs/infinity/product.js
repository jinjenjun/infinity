import { UNLOCKRequest, useErrorHandler } from '@/Libs/axios_vue.js';

export const getProductList = (data) =>
  useErrorHandler(
    UNLOCKRequest({
      method: 'GET',
      url: '/api/products',
      params: data,
    }),
  );