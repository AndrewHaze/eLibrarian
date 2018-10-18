const state = { link: "http://l.mgr.loc", possibility: true }

const getters = {
  prefix: state => state.link,
  login_form: state => state.possibility,
}

const mutations = {
  checkUsers(state, available) {
    state.possibility = available
  }
}

export default {
  state,
  getters,
  mutations,
}
