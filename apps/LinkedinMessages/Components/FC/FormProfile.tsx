import React from 'react';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import TableMessages from './TableMessages';

export interface FormProfileProps extends FormProps {}

const componentName = 'FormProfile'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormProfileProps) => {
  const picture: string = useRecordField('picture_url', '');
  return <div className='flex flex-col gap-2 md:flex-row'>
    <div className='flex-1'>
      <div className='flex flex-row *:w-1/2'>
        <Input field='first_name' customInputProps={{cssClass: 'text-2xl'}} />
        <Input field='last_name' customInputProps={{cssClass: 'text-2xl'}} />
      </div>
      <Input field='headline' />
      <Input field='company' />
      <Input field='location' />
      <Input field='email' />
      <Input field='profile_url' />
      <Input field='linkedin_urn' />
      <Input field='id_owner' />
    </div>
    <div className='flex-1'>
      {picture ? <img src={picture} alt='' className='w-24 h-24 rounded-full mb-2' /> : null}
      <Input field='note' customInputProps={{cssClass: 'bg-yellow-50 dark:bg-slate-600'}} />
    </div>
  </div>;
}

/** TabMessages */
const TabMessages = (props: FormProfileProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  if (form.id <= 0) return <div className='alert alert-info'>{T.translate('Create the profile first')}</div>;
  return <TableMessages
    tag={"table_profile_messages"}
    parentForm={form}
    uid={form.uid + "_table_profile_messages"}
    idProfile={form.id}
  />;
}


/** FormProfile */
const FormProfile = (props: FormProfileProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Profile'}
    urlSlug='linkedin-messages/profiles'
    tabs={{
      default: { title: <b>{T.translate('Profile')}</b>, content: () => <TabDefault {...props} /> },
      messages: { title: T.translate('Messages'), content: () => <TabMessages {...props} /> },
    }}
    title={{fields: ['first_name', 'last_name'], sub: T.translate('LinkedIn profile')}}
    {...props}
  ></Form>;
}

export default FormProfile;
