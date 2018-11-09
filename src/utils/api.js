const apiCall = ({
  url,
  data,
  method,
  ...args
}) => new Promise((resolve, reject) => {
  setTimeout(() => {
    try {
      if (method === 'POST') {
        const curentUserName = data.username;
        resolve({
          token: 'el',
          login: curentUserName
        })

      } else {
        const login = sessionStorage.getItem('user-login') || 'unkown';
        resolve({
          name: login
        })
      }
    } catch (err) {
      reject(new Error(err))
    }
  }, 1000)
})

export default apiCall
