import axios from 'axios';

const dataProvider = props => {

    const config = {
        baseURL: process.env.REACT_APP_API_ENTRYPOINT,
        params: {...props?.params},
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Basic ' + btoa('admin:admin'),
            'Accept': 'application/json',
            ...props?.headers
        }
    };

    return axios.create(config);
}

export default dataProvider;