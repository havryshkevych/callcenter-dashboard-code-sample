import React from 'react';
import 'tinymce/tinymce';
import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/autoresize';
import 'tinymce/plugins/autosave';
import 'tinymce/plugins/bbcode';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/code';
import 'tinymce/plugins/codesample';
import 'tinymce/plugins/colorpicker';
import 'tinymce/plugins/contextmenu';
import 'tinymce/plugins/directionality';
import 'tinymce/plugins/fullpage';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/help';
import 'tinymce/plugins/hr';
import 'tinymce/plugins/image';
import 'tinymce/plugins/imagetools';
import 'tinymce/plugins/importcss';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/legacyoutput';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/media';
import 'tinymce/plugins/nonbreaking';
import 'tinymce/plugins/noneditable';
import 'tinymce/plugins/pagebreak';
import 'tinymce/plugins/paste';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/print';
import 'tinymce/plugins/quickbars';
import 'tinymce/plugins/save';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/spellchecker';
import 'tinymce/plugins/tabfocus';
import 'tinymce/plugins/table';
import 'tinymce/plugins/template';
import 'tinymce/plugins/textcolor';
import 'tinymce/plugins/textpattern';
import 'tinymce/plugins/toc';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/visualchars';
import 'tinymce/plugins/wordcount';
import 'tinymce/skins/ui/oxide/skin.min.css';
import 'tinymce/skins/ui/oxide/content.min.css';
import 'tinymce/skins/content/default/content.min.css';
import {Editor} from '@tinymce/tinymce-react';
import {useInput} from 'react-admin';
import Typography from '@material-ui/core/Typography';
import tinymce from "tinymce";

function TextEditor(props) {
    const {
        input: {name, onChange, value},
        meta: {touched, error},
        isRequired
    } = useInput(props);

    const changeHandle = (content, editor) => {
        editor.dom.doc.querySelectorAll('table').forEach((table) => {
            if (!table.parentElement.classList.contains('table-wrapper')) {
                let wrapper = document.createElement('div');
                wrapper.classList.add('table-wrapper');
                wrapper.style.cssText = "overflow-x:auto";
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });
        onChange(editor.getContent());
    }
    const tinyMceInit = {
        height: props?.style?.height ? props.style.height : 650,
        menubar: true,
        selector: 'textarea',
        language: 'ru',
        plugins: [
            'advlist autolink lists link image imagetools charmap print preview anchor',
            'searchreplace visualblocks visualchars fullscreen',
            'insertdatetime media table pagebreak paste code codesample help wordcount'
        ],
        toolbar:
            'code | undo redo | formatselect | bold italic forecolor backcolor |' +
            'link anchor image media |' +
            'alignleft aligncenter alignright alignjustify |' +
            'bullist numlist outdent indent | removeformat',
        file_picker_types: 'image',
        imagetools_cors_hosts: ['blog.apteka24.ua', 'blog.z.apteka24.ua', 's3.eu-west-1.amazonaws.com', 'i.apteka24.ua', 'i-qa.apteka24.ua'],
        /* and here's our custom image picker*/
        file_picker_callback: function (cb) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.onchange = function () {
                var file = this.files[0];

                var reader = new FileReader();
                reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    /* call the callback and populate the Title field with the file name */
                    cb(blobInfo.blobUri(), {title: file.name});
                };
                reader.readAsDataURL(file);
            };

            input.click();
        }
    };
    return (
        <div style={{display: "grid"}}>
            {props.label && <Typography variant={"caption"}>{props.label}</Typography>}
            <Editor
                name={name}
                label={props.label}
                error={!!(touched && error)}
                required={isRequired}
                value={value}
                init={tinyMceInit}
                onEditorChange={changeHandle}
            />
        </div>
    );
}

export default TextEditor
