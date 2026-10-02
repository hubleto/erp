import React from 'react';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import MessageCalendarActivityForm from './MessageCalendarActivityForm';

export interface FormMessageActivityProps extends FormProps {}

/** FormMessageActivity: follow-ups use the shared calendar activity form */
const FormMessageActivity = (props: FormMessageActivityProps) => {
  return <MessageCalendarActivityForm {...props}></MessageCalendarActivityForm>;
}

export default FormMessageActivity;
