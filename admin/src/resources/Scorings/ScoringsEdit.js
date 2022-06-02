import React, {useEffect, useState} from 'react';
import {
    ArrayInput,
    Edit,
    SaveButton,
    SimpleForm,
    SimpleFormIterator,
    TextField,
    TextInput,
    Toolbar,
    useDataProvider,
    useInput,
    ReferenceInput,
    required,
    AutocompleteInput,
    DeleteButton
} from 'react-admin';
import { makeStyles } from '@material-ui/core/styles';
import debounce from 'lodash/debounce';
import Slider from '@material-ui/core/Slider';

const useStyles = makeStyles({
    critical: {
        color: '#bb0000',
    },
    warn: {
        color: '#bb7000',
    },
    normal: {
        color: '#22bb00',
    },
});

const EvaluationSlider = props => {
    const classes = useStyles();
    const marks = [{
        value: 0,
        label: <span style={{color: '#000000'}}>0</span>,
    }];

    const [sliderVal, setSliderVal] = useState(0);

    let criteria = props.criterias?.find(item => item.id === props.record.criteria);

    marks.push({
        value: (criteria?.max !== null ? criteria?.max : 10),
        label: <span style={{color: criteria?.critical || criteria?.max < 0 ? '#d20015' : '#15f600'}}>{criteria?.max ? criteria?.max : 10}</span>,
    });

    const {
        input: {value, name, onChange},
        isRequired
    } = useInput(props);

    const changeValue = React.useMemo(
        () =>
            debounce((newValue) => {
                onChange(newValue);
            }, 500),
        // eslint-disable-next-line
        [],
    );

    useEffect(()=> {
        if (criteria?.max) {
            if (criteria?.max < 0) {
                marks[0]['value'] = criteria?.max;
                marks[1]['value'] = 0;
            } else {
                marks[1]['value'] = criteria?.max;
            }
        }
        setSliderVal(value)
        // eslint-disable-next-line
    }, [criteria])

    if (marks[0].value > marks[1].value) {
        marks.reverse();
    }

    const handleChange = (event, newValue) => {
        setSliderVal(newValue)
        changeValue(newValue)
    };

    return (
            <Slider
                key={props.record['@id'] ?? props}
                className={criteria?.critical ? classes.critical : (marks[0].value < 0 ? classes.warn : classes.normal)}
                value={sliderVal}
                name={name}
                label={props.label}
                onChange={handleChange}
                required={isRequired}
                defaultValue={0}
                step={1}
                min={marks[0].value}
                max={marks[1].value}
                valueLabelDisplay="on"
                marks={marks}
            />
    )
}

const ScoringEdit = props => {
    const dataProvider = useDataProvider();
    const [criterias, setCriterias] = useState([]);
    const [evaluations, setEvaluations] = useState([]);
    useEffect(() => {
        let active = true;
        dataProvider.getList('admin/criterias', {
            pagination: {page: 1, perPage: 99},
            sort: {field: 'sort', order: 'ASC'},
            filter: {active: true},
        }).then((response) => {
            if (active) {
                setCriterias(response?.data)
            }
        });
        if (props.record?.evaluations) {
            setEvaluations(props.record.evaluations)
        }
        return () => {
            return active = false;
        }
        // eslint-disable-next-line
    }, [props.record]);
    useEffect(() => {
        let active = true;
        if (evaluations.length) {
            evaluations.sort((a, b) => {
                return criterias.find(e => e.id === a.id).sort > criterias.find(e => e.id === b.id).sort;
            })
            setEvaluations(evaluations)
        }
        return () => {
            return active = false;
        }
        // eslint-disable-next-line
    }, [criterias]);
    const CustomToolbar = props => (
        <Toolbar {...props} style={{
            display: "flex",
            justifyContent: "space-between"
        }}>
            <SaveButton disabled={props.pristine}/>
            <DeleteButton mutationMode="pessimistic" redirect={false}/>
        </Toolbar>
    );
    const CriteriaDescription = props => {
        let criteria = props.criterias?.find(item => item.id === props.record.criteria);
        return <div style={{paddingTop: 10, paddingBottom: 35}}>
            <TextField variant={"caption"} record={criteria} source="title"/>
            <br/>
            <TextField variant={"caption"} record={criteria} source="description"/>
        </div>;
    }
    let searchFilter = searchText => {
        let result = {name: [searchText], id: []};
        if (props.users) {
            props.users.forEach((user, index) => {
                result.id[index] = user;
            })
        }
        return result;
    };
    const SortedIterator = props => {
        if ((!props.record && props.record.evaluations === undefined) || !criterias) {
            return null;
        }

        props.record.evaluations.sort((a, b) => {
            if (criterias.find(e => e.id === a.criteria)?.sort < criterias.find(e => e.id === b.criteria)?.sort) {
                return -1;
            }
            if (criterias.find(e => e.id === a.criteria)?.sort > criterias.find(e => e.id === b.criteria)?.sort) {
                return 1;
            }
            if (criterias.find(e => e.id === a.criteria)?.sort === criterias.find(e => e.id === b.criteria)?.sort) {
                return 0;
            }
        });

        return <SimpleFormIterator getItemLabel={index => ""} disableRemove disableAdd disableReordering {...props}>
            <CriteriaDescription criterias={criterias}/>
            <EvaluationSlider criterias={criterias} label={"Оценка"} source="value"/>
            <TextInput multiline variant={"outlined"} label={"Комментарий"} source="comment" fullWidth/>
        </SimpleFormIterator>;
    }
    return (
        <Edit {...props} mutationMode="pessimistic">
            <SimpleForm redirect={false} toolbar={<CustomToolbar/>}>
                <ReferenceInput label="Operator" source ="user"
                                reference="admin/users" fullWidth
                                filterToQuery={searchFilter}
                >
                    <AutocompleteInput optionText="name" fullWidth allowEmpty={false} validate={[required()]}/>
                </ReferenceInput>
                <ArrayInput source="evaluations" label="">
                    <SortedIterator getItemLabel={index => ""} disableRemove disableAdd disableReordering/>
                </ArrayInput>
            </SimpleForm>
        </Edit>
    );
}

export default ScoringEdit;