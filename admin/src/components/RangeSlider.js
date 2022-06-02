import React, {useEffect, useState} from 'react';
import {
    useInput
} from 'react-admin';
import debounce from 'lodash/debounce';
import Slider from '@material-ui/core/Slider';

const RangeSlider = (props) => {
    const [sliderVal, setSliderVal] = useState([0,100]);
    const {
        input: {value:valueA, name:nameA, onChange:onChangeA}
    } = useInput({...props, source:props.source[0]});
    const {
        input: {value:valueB, name:nameB, onChange:onChangeB}
    } = useInput({...props, source:props.source[1]});
    const changeValue = React.useMemo(
        () =>
            debounce((newValue) => {
                onChangeA(newValue[0]);
                onChangeB(newValue[1]);
            }, 500),
        // eslint-disable-next-line
    []);
    const handleChange = (event, newValue) => {
        setSliderVal(newValue)
        changeValue(newValue)
    };

    useEffect(()=> {
        setSliderVal([valueA, valueB])
        // eslint-disable-next-line
    }, [props.record])

    return <Slider
        value={sliderVal}
        label={props.label}
        name={nameA + nameB}
        onChange={handleChange}
        defaultValue={[0,100]}
        step={1}
        min={0}
        max={100}
        valueLabelDisplay="auto"
    />
}

export default RangeSlider;