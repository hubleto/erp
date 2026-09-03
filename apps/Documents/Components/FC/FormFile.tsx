import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';

export interface FormFileProps extends FormProps {}

const componentName = 'FormFile'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormFileProps) => {
  const form = React.useContext(FormMetaContext);
  const uid: string = useRecordField('uid', '');
  const hyperlink: string = useRecordField('hyperlink', '');
  const FOLDER: any = useRecordField('FOLDER', {});
  const downloadUrl = hyperlink ?? globalThis.hubleto.config.projectUrl + '/documents/files/download?fld=' + (FOLDER.uid ?? '') + '&file=' + uid;

  return <div className='flex gap-2 h-full'>
    <div className='flex-1'>
      <Input field='id_folder' />
      <Input field='name' customInputProps={{cssClass: 'text-2xl'}} />
      <Input field='file' />
      <Input field='hyperlink' />
      <Input field='is_public' />
      <div className='mt-16 text-center'>
        <a
          href={downloadUrl}
          target='_blank'
          className='btn btn-extra-large btn-primary-outline'
        >
          <span className='icon'><i className='fas fa-download'></i></span>
          <span className='text'>{T.translate('Download')}</span>
        </a>
      </div>
    </div>
    <div className='flex-2'>
      <div className='card h-full'>
        <div className='card-header'>{T.translate('Preview')}</div>
        <div className='card-body h-full'>
          <iframe className='w-full h-full' src={downloadUrl}></iframe>
        </div>
      </div>
    </div>
  </div>;
}

/** FormFile */
const FormFile = (props: FormFileProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/File'}
    urlSlug='documents/files'
    title={{field: 'name', sub: T.translate('File')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormFile;
