import React from 'react';
import TextField from '@material-ui/core/TextField';
import Autocomplete from '@material-ui/lab/Autocomplete';
import Grid from '@material-ui/core/Grid';
import throttle from 'lodash/throttle';
import {useInput} from 'react-admin';
import Chip from '@material-ui/core/Chip';
import Avatar from '@material-ui/core/Avatar';
import Tooltip from '@material-ui/core/Tooltip';

const getProductPredictions = async (value, callback) => {
    const inputValue = value.input.trim().toLowerCase();
    let response = await fetch(
        process.env.REACT_APP_CATALOGUE_API_ENTRYPOINT + "/projections?translations.name=" + inputValue,
        {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + process.env.REACT_APP_CATALOGUE_TOKEN
            }
        }
    );
    callback(await response.json());
};
const getProductProjection = async (value, callback) => {
    const inputValue = value.input.trim().toLowerCase();
    let response = await fetch(
        process.env.REACT_APP_CATALOGUE_API_ENTRYPOINT + "/projections?id=" + inputValue,
        {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Basic ' + process.env.REACT_APP_CATALOGUE_TOKEN
            }
        }
    );
    callback(await response.json());
};

const ProductAutoCompleteInput = props => {
    const [myValue, setValue] = React.useState([]);
    const [inputValue, setInputValue] = React.useState('');
    const [options, setOptions] = React.useState([]);

    const {
        input: {name, onChange, value, ...rest},
        meta: {touched, error}
    } = useInput(props);

    const fetch = React.useMemo(
        () =>
            throttle((request, callback) => {
                getProductPredictions(request, callback);
            }, 200),
        [],
    );

    React.useEffect(() => {
        let active = true;
        let initial = value.map(async prod => {
            await getProductProjection({input: prod.replace("/api/products/", "")}, (results) => {
                if (active) {
                    prod = results[0];
                }
            });
            return await prod;
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
            id="catalogue-products"
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
            error={!!(touched && error)}
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
                <TextField {...params} label="Добавить продукт" variant="outlined" fullWidth/>
            )}
            renderTags={(value, getTagProps) =>
                value.map((option, index) => {
                    return (
                        <Tooltip key={index} title={
                            <div><img src={option?.preview?.src?.medium} alt={option.name}/>
                                <p>{option.sku}</p>
                                <p>{option.name}</p>
                            </div>
                        }>
                            <Chip label={option.name} avatar={<Avatar alt="Natacha"
                                                                      src={option?.preview?.src?.small}/>} {...getTagProps({index})}/>
                        </Tooltip>
                    )
                })
            }
            renderOption={(option) => {
                return (
                    <Grid container alignItems="center">
                        <Grid item>
                            {option?.preview?.src?.small &&
                            <img src={option?.preview?.src?.small} alt={option.name}
                                 style={{width: "50px", maxHeight: "60px"}}/>}
                        </Grid>
                        <Grid item xs>
                            <span>{option.name}</span>
                        </Grid>
                    </Grid>
                );
            }}
        />
    );
}

export default ProductAutoCompleteInput;
