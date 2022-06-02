import React from 'react';
import {AutocompleteInput} from 'react-admin';

const getSuggestionsById = async (value) => {
    let response = await fetch(
        process.env.REACT_APP_UCB_API_ENTRYPOINT + "/employee-profiles/" + value,
        {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + process.env.REACT_APP_UCB_TOKEN
            }
        }
    );

    return await response.json()
};

const getSuggestions = async (value) => {
    const inputValue = value.trim().toLowerCase();
    let response = await fetch(
        process.env.REACT_APP_UCB_API_ENTRYPOINT + "/employee-profiles?fullName=" + inputValue,
        {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + process.env.REACT_APP_UCB_TOKEN
            }
        }
    );
    return await response.json()
};

const optionRenderer = choice => {
    return (choice ?
        (typeof choice.lastName === "undefined" ? '' : choice.lastName + ' ') +
        (typeof choice.name === 'undefined' ? '' : choice.name) +
        (typeof choice.middleName === "undefined" ? '' : ' ' + choice.middleName) : '');
}

const UserAutoCompleteInput = props => {
    let timer = 0;
    const [choices, setChoices] = React.useState([])
    const [selected] = React.useState([])
    const onChange = event => {
        let value = event.target.value;

        if (value === '') {
            return;
        }

        clearTimeout(timer);
        timer = setTimeout(function () {
            getSuggestions(value)
                .then(data => {
                    if (data.Error) {
                        setChoices([]);
                    } else {
                        setChoices(data);
                    }
                })
        }, 2000);
    }

    React.useEffect(() => {
        if (props?.record[props?.source]) {
            getSuggestionsById(props.record[props.source]).then(data => {
                if (data.id) {
                    setChoices([selected, data])
                }
            })
        }
    }, [props.record, props.source, selected]);

    return (<AutocompleteInput
        {...props}
        choices={choices}
        options={{
            onKeyUp: onChange
        }}
        optionText={optionRenderer}
        fullWidth={true}
        allowEmpty={true}
    />);
}

export default UserAutoCompleteInput;
