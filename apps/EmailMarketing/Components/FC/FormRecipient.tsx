import React, { useState } from 'react';
import { FormMeta, FormProps, FormRecord } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Translator from '@hubleto/react-ui/core/Translator';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import request from '@hubleto/react-ui/core/Request';

export interface FormRecipientProps extends FormProps {}

const componentName = 'FormRecipient';
const parentApp = 'Hubleto/App/Community/EmailMarketing';
const T = new Translator(parentApp + '/Loader', 'Components/FormRecipient');

/** TabDefault */
const TabDefault = ({ parent }) => {
  const idCampaign = useRecordField('id_campaign', 0);

  return <div className='w-full flex gap-2'>
    <div className='flex-1 border-r border-gray-100'>
      <Input field='id_campaign' />
      {idCampaign == 0 ? <Input field='id_email' /> : null}
      <Input field='id_contact' />
      <Input field='email' />
      <Input field='variables' />
      <Input field='notes' />
    </div>
    <div className='flex-1 gap-2'>
      <div className='card'>
        <div className='card-header'>{T.translate('Mail preview')}</div>
        <div className='card-body'>
          {parent.mailPreviewInfo && parent.mailPreviewInfo.bodyHtml != '' ? <>
            <div
              dangerouslySetInnerHTML={{__html: parent.mailPreviewInfo.bodyHtml}}
            ></div>
          </> : <div>
            {T.translate('No mail preview available.')}
          </div>}
        </div>
      </div>
      {parent.mailPreviewInfo && parent.mailPreviewInfo.scheduledMails && Object.keys(parent.mailPreviewInfo.scheduledMails).length > 0 ? <>
        <div className='card'>
          <div className='card-header'>{T.translate('Scheduled emails')}</div>
          <div className='card-body flex flex-col gap-2'>
            {Object.keys(parent.mailPreviewInfo.scheduledMails).map((key) => {
              const schedule = parent.mailPreviewInfo.scheduledMails[key];
              return <div className='badge' key={key}>
                {schedule.MAIL.datetime_scheduled_to_send}
                &nbsp;
                <b>{schedule.MAIL.subject}</b>
              </div>;
            })}
          </div>
        </div>
      </> : null}
    </div>
  </div>;
}

/** FormRecipient */
const FormRecipient = (props: FormRecipientProps) => {

  const [mailPreviewInfo, setMailPreviewInfo] = useState(null);

  const myself = {
    props,
    mailPreviewInfo,
    setMailPreviewInfo,
  }

  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Recipient'}
    urlSlug='email-marketing/emails/recipients'
    endpointParams={{saveRelations: ['TAGS'] }}
    onAfterFormInitialized={(form: FormMeta) => {
      form.setReadonly(form.recordStore.getField('is_closed') == 1);
    }}
    onAfterRecordLoaded={(form: FormMeta, record: FormRecord) => {
      request.post(
        'email-marketing/api/get-email-preview-info',
        { idRecipient: record.id },
        {},
        (result: any) => { setMailPreviewInfo(result); }
      );
    }}
    title={{field: 'email', sub: T.translate('Recipient')}}
    tabs={{default: {content: () => <TabDefault parent={myself} />}}}
    {...props}
  ></Form>;
}

export default FormRecipient;