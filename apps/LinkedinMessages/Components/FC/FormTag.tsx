import React from 'react';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormTagProps extends FormProps {}

const componentName = 'FormTag'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormTagProps) => {
  return <div className='flex flex-col gap-2'>
    <Input field='name' customInputProps={{cssClass: 'text-2xl'}} />
    <Input field='color' />
  </div>;
}


/** FormTag */
const FormTag = (props: FormTagProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Tag'}
    urlSlug='linkedin-messages/tags'
    tabs={{
      default: { title: <b>{T.translate('Tag')}</b>, content: () => <TabDefault {...props} /> },
    }}
    title={{fields: ['name'], sub: T.translate('Message tag')}}
    {...props}
  ></Form>;
}

export default FormTag;
