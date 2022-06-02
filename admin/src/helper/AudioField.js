import React from 'react';

const AudioField = props => {
    const [duration, setDuration] = React.useState(0);
    return <>
        <audio controls src={props.record[props.source]} onLoadedMetadata={event => {setDuration(event.target.duration)}}>
            <p>Ваш браузер не поддерживает HTML5 аудио. Вот взамен
                <a href={props.record[props.source]} target={"_blank"} rel="noopener noreferrer">ссылка на аудио</a></p>
        </audio>
        {duration && props?.showTime && <div>{parseInt(duration)} seconds</div>}
    </>;
}

export default AudioField;