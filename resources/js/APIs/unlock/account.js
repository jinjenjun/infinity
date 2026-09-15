import { UNLOCKRequest } from '@/Libs/axios_vue.js';

export const login = (data) =>
  UNLOCKRequest({
    method: 'POST',
    url: route('login'),
    data,
  });

export const logout = (url) =>
  UNLOCKRequest({
    method: 'POST',
    url,
  });
