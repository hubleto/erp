import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormNotificationProps extends FormProps {}

const componentName = 'FormNotification'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Notifications';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormNotificationProps) => {
  return <div className='flex gap-2'>
    <div className='flex-3'>
      <Input field='id_to' />
      <Input field='subject' />
      <Input field='url' />
      <Input field='model' />
      <Input field='record_id' />
      <Input field='body' />
    </div>
    <div className='flex-1'>
      <Input field='id_from' />
      <Input field='priority' />
      <Input field='category' />
      <Input field='tags' />
      <Input field='datetime_sent' />
    </div>
  </div>;
}

/** FormNotification */
const FormNotification = (props: FormNotificationProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Notification'}
    urlSlug='parent-app-slug/same-url-slug-as-in-table'
    endpointParams={{}}
    onAfterFormInitialized={(form: any) => {}}
    title={{field: 'subject', sub: T.translate('Notification')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormNotification;
