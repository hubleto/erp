import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';

export interface FormMailProps extends FormProps {
  idAccount: number,
  idMailbox: number,
}

const componentName = 'FormMail'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Mail';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormMailProps) => {
  const form = React.useContext(FormMetaContext);
  const datetimeSent = useRecordField('datetime_sent');
  const ATTACHMENTS: any = useRecordField('ATTACHMENTS', {});

  return <div className='flex gap-2'>
    <div className='flex-3'>
      <Input field='from' />
      <Input field='subject' customInputProps={{cssClass: 'text-2xl'}} />
      <Input field='body_html' customInputProps={{readonly: datetimeSent !== null}} />
    </div>
    <div className='flex-1'>
      {ATTACHMENTS ? <div className='flex gap-2 flex-wrap'>{ATTACHMENTS.map((att, key) => {
        return <div className='mt-2'>
          <a
            href={globalThis.hubleto.config.uploadUrl + '/' + att.file}
            className='btn btn-blue-outline'
            target='_blank'
          >
            <span className='icon'><i className='fas fa-link'></i></span>
            <span className='text'>{att.name} ({Math.round(att.size/1024*100)/100} kB)</span>
          </a>
        </div>
      })}</div> : null}
      <Input field='to' />
      <Input field='cc' />
      <Input field='bcc' />
      <Input field='reply_to' />
      <Input field='in_reply_to' />
      <Input field='datetime_created' customInputProps={{readonly: true}} />
      <Input field='datetime_scheduled_to_send' customInputProps={{readonly: true}} />
      <Input field='datetime_sent' customInputProps={{readonly: true}} />
      <Input field='datetime_read' customInputProps={{readonly: true}} />
    </div>
  </div>;
}

/** FormMail */
const FormMail = (props: FormMailProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Mail'}
    urlSlug={'mail/' + props.idAccount + '/' + props.idMailbox}
    endpointParams={{}}
    // onAfterFormInitialized={(form: any) => {}}
    // renderTitle={(): React.JSX.Element => { return <></>; }
    title={{field: 'subject', sub: T.translate('Mail')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormMail;
