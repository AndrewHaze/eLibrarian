const state = { link: "http://l.mgr.loc", connected: false }

const getters = {
  prefix: state => state.link,
  isConnected: state => state.connected,
}



const mutations = {
}

export default {
  state,
  getters,
  mutations,
}
