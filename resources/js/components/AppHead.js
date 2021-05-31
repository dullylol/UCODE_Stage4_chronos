import Cookie from "js-cookie"
import ReactDOM from 'react-dom';

export default function AppHead() {


    if (!Cookie.get('token')) {
        return (
            <div style={styles.head}>
                <a href="/" style={styles.aStyle}><h1 style={styles.headText}>Chronos</h1></a>
                <div style={styles.auth}>
                    <a style={styles.login} href="/login">Login</a>
                    <a style={styles.register} href="/registration">Registration</a>
                </div>
            </div>
        );
    } else {
        return (
            <>
            <div style={styles.head}>
                <a href="/" style={styles.aStyle}><h1 style={styles.headText}>Chronos</h1></a>

            <div style={styles.auth}>
                    <a style={styles.login} href="/" onClick={onLogOut}>Log out</a>
                </div>
            </div>
            </>
        );
    }
  }

  function onLogOut() {
    Cookie.remove('token')
    Cookie.remove('user_id')
    window.location.reload()
  }
  
  const styles = {
    head: {
        background: 'linear-gradient(Indigo, #9198e5)',
        border: '3px solid black',
        borderRadius: '0 0 8px 8px ',
        display: 'flex',
        justifyContent: 'space-between',

    },

    logo: {
        maxWidth: '60px',
        maxHeight: '60px'
    },

    aStyle: {
        marginTop: 'auto',
        marginBottom: 'auto',
        textDecoration: 'none'
    },

    headText: {
        color: 'LemonChiffon',
        fontFamily: 'Monospace',
        textAlign: 'center',
        fontSize: '3em',
        margin: '10px',
        marginTop: 'auto',
        marginBottom: 'auto',
        wordBreak: 'break-all'
    },

    auth: {
        float: 'right',
        textAlign: 'right',
        marginTop: 'auto',
        marginBottom: 'auto',
        marginRight: '20px',
    },

    login: {
        color: 'LemonChiffon',
        display: 'block',
        textDecoration: 'none'
    },

    register: {
        color: 'LemonChiffon',
        display: 'block',
        textDecoration: 'none'
    },


    menu_bar_as: {
        color: 'LemonChiffon',
        margin: '10px',
        fontSize: '18px'
    }
  }

if (document.getElementById('app-head')) {
    ReactDOM.render(<AppHead />, document.getElementById('app-head'));
}