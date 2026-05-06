export const musicStore = {
  namespaced: true,
  state: () => ({
    musicStatus: {},
  }),
  mutations: {
    updateMusic(state, musicItem) {
      state.musicStatus = musicItem;
    },
  },
  actions: {
    updateMusic({ commit }, musicItem) {
      commit('updateMusic', musicItem);
    },
  },
  getters: {
    getMusic: (state) => state.musicStatus,
  },
};

export const getMusicData = (store) => store.getters['music/getMusic'];

export const setMusicData = (store, musicItem) => {
  store.dispatch('music/updateMusic', musicItem);
};
