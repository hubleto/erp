import React from 'react';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormReplyProps extends FormProps {}

const componentName = 'FormReply'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Custom/LinkedinMessages';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormReplyProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  const status: number = useRecordField('status', 0);
  const errorInfo: string = useRecordField('error_info', '');

  return <div className='flex flex-col gap-2'>
    {(status == 2 || status == 3) ? <div className='alert alert-warning'>
      {T.translate('This reply has not been delivered to LinkedIn yet. Send it manually and set the status to "Sent manually".')}
    </div> : null}
    {errorInfo ? <div className='alert alert-info text-xs'>{errorInfo}</div> : null}
    <Input field='id_message' />
    <Input field='body' />
    <Input field='status' />
    <Input field='sent_at' />
    <Input field='id_owner' />
  </div>;
}


/** FormReply */
const FormReply = (props: FormReplyProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Reply'}
    urlSlug='linkedin-messages/replies'
    tabs={{
      default: { title: <b>{T.translate('Reply')}</b>, content: () => <TabDefault {...props} /> },
    }}
    title={{fields: ['body'], sub: T.translate('Reply')}}
    {...props}
  ></Form>;
}

export default FormReply;
