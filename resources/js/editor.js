import {
    ClassicEditor,
    Alignment,
    BlockQuote,
    Bold,
    Code,
    CodeBlock,
    Essentials,
    Heading,
    HorizontalLine,
    Image,
    ImageCaption,
    ImageInsert,
    ImageResize,
    ImageStyle,
    ImageToolbar,
    ImageUpload,
    Indent,
    Italic,
    Link,
    List,
    MediaEmbed,
    Paragraph,
    SimpleUploadAdapter,
    Strikethrough,
    Table,
    TableCellProperties,
    TableProperties,
    TableToolbar,
    Underline,
} from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';
import '../css/editor.css';

/**
 * Khởi tạo CKEditor 5 cho mọi <textarea data-editor>.
 * Ảnh được upload qua route('upload.image') (UploadController) bằng SimpleUploadAdapter.
 */
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

document.querySelectorAll('textarea[data-editor]').forEach((textarea) => {
    ClassicEditor.create(textarea, {
        licenseKey: 'GPL',
        plugins: [
            Essentials, Paragraph, Heading, Bold, Italic, Underline, Strikethrough, Code,
            Link, List, Indent, Alignment, BlockQuote, CodeBlock, HorizontalLine,
            Image, ImageCaption, ImageInsert, ImageResize, ImageStyle, ImageToolbar, ImageUpload, SimpleUploadAdapter,
            Table, TableToolbar, TableProperties, TableCellProperties,
            MediaEmbed,
        ],
        toolbar: {
            items: [
                'undo', 'redo', '|',
                'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', 'code', '|',
                'link', 'insertImage', 'mediaEmbed', 'insertTable', 'blockQuote', 'codeBlock', 'horizontalLine', '|',
                'alignment', 'bulletedList', 'numberedList', 'outdent', 'indent',
            ],
            shouldNotGroupWhenFull: false,
        },
        placeholder: textarea.getAttribute('placeholder') || '',
        image: {
            toolbar: [
                'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|',
                'toggleImageCaption', 'imageTextAlternative', 'resizeImage',
            ],
        },
        table: {
            contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties'],
        },
        // Lưu thẳng <iframe> vào HTML để trang học bài hiển thị được video mà không cần JS của CKEditor.
        mediaEmbed: {
            previewsInData: true,
        },
        simpleUpload: {
            uploadUrl: textarea.dataset.uploadUrl,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
        },
    }).catch((error) => {
        console.error('Không khởi tạo được CKEditor:', error);
    });
});
