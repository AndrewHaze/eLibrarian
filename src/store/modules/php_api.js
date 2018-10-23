const state = { link: "http://l.mgr.loc", possibility: true, reg: false, at: 'eLibrarian v.0.0.1' }

const getters = {
  //домен для подстановки в пути к файлам
  prefix: state => state.link,
  //флаг: при отсутвии юзеров на экране рег. не показываем ссылку на вход
  loginForm: state => state.possibility,
  //флаг: окно после регистрации 
  congratulation: state => state.reg,
  appTitle: state => state.at,
}

const mutations = {
  checkUsers(state, available) {
    state.possibility = available
  },
  showRegModal(state, show) {
    state.reg = show
  }
}

export default {
  state,
  getters,
  mutations,
}
