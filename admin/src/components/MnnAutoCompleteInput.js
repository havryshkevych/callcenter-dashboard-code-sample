import React from 'react';
import TextField from '@material-ui/core/TextField';
import Autocomplete from '@material-ui/lab/Autocomplete';
import throttle from 'lodash/throttle';
import {useInput} from 'react-admin';
import Chip from '@material-ui/core/Chip';

const getMnnPredictions = async (value, callback) => {
    const inputValue = value.input.trim().toLowerCase();
    let response = await fetch(
        process.env.REACT_APP_CATALOGUE_API_ENTRYPOINT + "/marketed-names?translations.name=" + inputValue,
        {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + process.env.REACT_APP_CATALOGUE_TOKEN
            }
        }
    );
    callback(await response.json());
};
const getMnn = async (value, callback) => {
    const inputValue = value.input.trim().toLowerCase();
    let response = await fetch(
        process.env.REACT_APP_CATALOGUE_API_ENTRYPOINT + "/marketed-names/" + inputValue,
        {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + process.env.REACT_APP_CATALOGUE_TOKEN
            }
        }
    );
    callback(await response.json());
};

const MnnAutoCompleteInput = ({mutationMode, ...props}) => {
    const [myValue, setValue] = React.useState([]);
    const [inputValue, setInputValue] = React.useState('');
    const [options, setOptions] = React.useState([]);

    const {
        input: {name, onChange, value, ...rest},
    } = useInput(props);

    const fetch = React.useMemo(
        () =>
            throttle((request, callback) => {
                getMnnPredictions(request, callback);
            }, 200),
        [],
    );

    React.useEffect(() => {
        let active = true;
        let initial = value.map(async mnn => {
            await getMnn({input: mnn.replace("/api/marketed-names/", "")}, (results) => {
                if (active) {
                    mnn = results;
                }
            });
            return await mnn;
        });
        Promise.all(initial).then(data => {
            if (active) {
                setValue(data)
            }
        });
        return () => {
            active = false;
        };
        // eslint-disable-next-line
    }, []);

    React.useEffect(() => {
        let active = true;

        if (inputValue === '') {
            setOptions(myValue !== null ? [myValue] : []);
            return undefined;
        }

        fetch({input: inputValue}, (results) => {
            if (active) {
                let newOptions = [];

                if (myValue) {
                    newOptions = [myValue];
                }

                if (results) {
                    newOptions = [...newOptions, ...results];
                }

                setOptions(newOptions);
            }
        });

        return () => {
            active = false;
        };
    }, [myValue, inputValue, fetch]);

    return (
        <Autocomplete
            id="catalogue-mnns"
            style={{margin: "0 0 20px 0"}}
            name={name}
            multiple
            label={props.label}
            getOptionLabel={(option) => (typeof option === 'string' ? option : (option?.name ? option?.name : option?.id))}
            filterOptions={(x) => x}
            options={options}
            autoComplete
            includeInputInList
            filterSelectedOptions
            value={myValue}
            {...rest}
            getOptionSelected={(option, value) => option.id === value.id}
            onChange={(event, newValue) => {
                setOptions(newValue ? [newValue, ...options] : options);
                setValue(newValue);
                onChange(newValue);
            }}
            onInputChange={(event, newInputValue) => {
                setInputValue(newInputValue);
            }}
            renderInput={(params) => (
                <TextField {...params} label="Добавить МНН" variant="outlined" fullWidth/>
            )}
            renderTags={(value, getTagProps) =>
                value.map((option, index) => {
                    return (<Chip label={option.name} {...getTagProps({index})}/>);
                })
            }
            renderOption={(option) => {
                return (<span>{option.name}</span>);
            }}
        />
    );
};

export default MnnAutoCompleteInput;
