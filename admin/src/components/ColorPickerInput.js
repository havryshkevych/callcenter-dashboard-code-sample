import React, {useState} from 'react'
import reactCSS from 'reactcss'
import {useInput} from "react-admin";
import {PhotoshopPicker} from 'react-color'
import TextField from '@material-ui/core/TextField';

const ColorPickerInput = props => {
    const {
        input: {name, onChange, value, ...rest},
        meta: {touched, error},
        isRequired
    } = useInput(props);

    const hexToRgb = (hex) => {
        let result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16),
            a: 1
        } : {
            r: '255',
            g: '255',
            b: '255',
            a: '1',
        };
    }

    const [displayPicker, setDisplayPicker] = useState(false);
    const [color, setColor] = useState(props.record ? props.record[props.source] : '');
    const [colorRGB, setColorRGB] = useState(props.record && props.record[props.source] ? hexToRgb(props.record[props.source]) : {
        r: '255',
        g: '255',
        b: '255',
        a: '1',
    });

    const handleClick = () => {
        setDisplayPicker(!displayPicker);
    };

    const handleClose = () => {
        setDisplayPicker(false);
    };

    const handleChange = (color) => {
        setColor(color.hex);
        setColorRGB(hexToRgb(color.hex));
        onChange(color.hex);
    };

    const styles = reactCSS({
        'default': {
            color: {
                position: 'absolute',
                width: '35px',
                height: '35px',
                borderRadius: '2px',
                background: `${color}`,
                top: '15px',
                right: '15px'
            },
            swatch: {
                position: 'relative',
                padding: '5px',
                background: '#fff',
                display: 'inline-block',
                cursor: 'pointer',
            },
            popover: {
                position: 'absolute',
                zIndex: '2',
            },
            cover: {
                position: 'fixed',
                top: '0px',
                right: '0px',
                bottom: '0px',
                left: '0px',
            }
        },
    });

    return (
        <div>
            <div style={styles.swatch} onClick={handleClick}>
                <TextField
                    name={name}
                    label={props.label}
                    onChange={onChange}
                    error={!!(touched && error)}
                    required={isRequired}
                    variant="filled"
                    value={color}
                    {...rest}
                />
                <div style={styles.color}/>
            </div>
            {displayPicker ? <div style={styles.popover}>
                <div style={styles.cover} onClick={handleClose}/>
                <PhotoshopPicker color={colorRGB} onChange={handleChange}/>
            </div> : null}
        </div>
    )
}

export default ColorPickerInput;
