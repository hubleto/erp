import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import TableReviews from './TableReviews';

export interface FormVersionProps extends FormProps {}

const componentName = 'FormVersion'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/AppXXX';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormVersionProps) => {
  return <div className='flex gap-2 h-full'>
    <div className='flex-3'>
      <Input field='uid' customInputProps={{readonly: true}} />
      <Input field='id_document' />
      <Input field='version' />
      <Input field='file' />
      <Input field='created_on' />
      <Input field='id_created_by' />
    </div>
    <div className='flex-1'>
      <TableReviews
        tag={"table_documents_versions_reviews"}
        parentForm={this}
        uid={props.uid + "_table_documents_versions_reviews"}
        idDocument={props.id}
        idVersion={props.id}
      />
    </div>
  </div>;
}

/** FormVersion */
const FormVersion = (props: FormVersionProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Version'}
    urlSlug='documents/versions'
    endpointParams={{}}
    onAfterFormInitialized={(form: any) => {}}
    title={{field: 'name', sub: T.translate('Version')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormVersion;
