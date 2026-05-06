export const memberStore = {
  namespaced: true,
  state: () => ({
    loginData: {},
  }),
  mutations: {
    updateLogin(state, loginItem) {
      state.loginData = loginItem;
    },
  },
  actions: {
    updateLogin({ commit }, loginItem) {
      commit('updateLogin', loginItem);
    },
  },
  getters: {
    getLogin: (state) => state.loginData,
  },
};

export const getLoginData = (store) => store.getters['member/getLogin'];

export const setLoginData = (store, loginItem) => {
  store.dispatch('member/updateLogin', loginItem);
};
