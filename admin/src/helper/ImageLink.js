import React from 'react';
import {ImageField} from 'react-admin';

const ImageLink = props => {
    return <a href={props['record'][props.source]} target={"_blank"} rel="noopener noreferrer">
        <ImageField source={"screenshot"} {...props}/>
    </a>;
}

export default ImageLink;