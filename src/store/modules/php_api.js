const state = { link: "http://l.mgr.loc", possibility: true }

const getters = {
  //домен для подстановки в пути к файлам
  prefix: state => state.link,
  //при отсутвии юзеров с формы входа перекидываем на регистрацию
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
