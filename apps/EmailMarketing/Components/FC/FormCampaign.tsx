import React, { useState, useRef } from 'react';
import TableCampaignsSchedules from './TableCampaignsSchedules';
import TableRecipients from './TableRecipients';
import request from '@hubleto/react-ui/core/Request';
import Translator from "@hubleto/react-ui/core/Translator";
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import { FormMeta } from '@hubleto/react-ui/components/fc/FormInterfaces';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import { FormProps, FormTabs } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import InputTags from '@hubleto/react-ui/components/fc/Inputs/Tags';

export interface FormCampaignProps extends FormProps {}

const translate = new Translator(
  'Hubleto\\App\\Community\\EmailMarketing\\Loader',
  'Components\\FormCampaign'
).translate;

/**
 * Title
 *
 * @var [type]
 */
const Title = (props: FormCampaignProps) => <>
  <small>{translate('Campaign')}</small>
  <h2>{useRecordField('title') ?? '-'}</h2>
</>;

/**
 * TabDefault
 *
 * @var [type]
 */
const TabDefault = (props: FormCampaignProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);
  const TAGS = useRecordField('TAGS');

  return <div className='flex gap-2 flex-col md:flex-row'>
    <div className='grow'>
      <Input field='title' />
      <Input title={translate('Tags')}>
        <InputTags
          field='TAGS'
          value={TAGS}
          model='Hubleto/App/Community/EmailMarketing/Models/Tag'
          targetColumn='id_campaign'
          sourceColumn='id_tag'
          colorColumn='_LOOKUP_COLOR'
          showSelect={false}
          showTagButtons={true}
            onChange={(input: any, value: any) => {
              form.changeField(input, value);
            }}
          onNewTag={(title: string) => {
            return { id: -1, name: title, color: '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0') }
          }}
        ></InputTags>
      </Input>

      <Input field='target_audience' />
      <Input field='goal' />
      <Input field='notes' />
    </div>
    {form.id <= 0 ? null : <>
      <div className='grow'>
        <TableCampaignsSchedules
          tag='table_campaign_schedules'
          parentForm={this}
          uid={form.uid + "_table_campaign_schedules"}
          idCampaign={form.id}
        />
      </div>
    </>}
  </div>;
}

/**
 * TabRecipients
 *
 * @var [type]
 */
const TabRecipients = (props: FormCampaignProps) => {
  const form: FormMeta = React.useContext(FormMetaContext);

  const example1 = ["recipient@example.com", {"name": "John Smith", "age": 21}];
  const example2 = ["john.smith@example.com", {"name": "John Smith"}];

  const refTableRecipients = useRef(null);
  const refEmails = useRef(null);

  const [recipients, setRecipients] = useState([]);

  return <div className='flex gap-2'>
    <div className='flex-3'>
      <TableRecipients
        tag='table_email_recipients'
        //@ts-ignore
        ref={refTableRecipients}
        parentForm={this}
        uid={form.uid + "_table_email_recipient"}
        idCampaign={form.id}
        view='briefOverview'
        onAfterLoadData={(table: any) => {
          setRecipients(table.state.data.records);
        }}
      />
    </div>
    <div className='flex-2 gap-2'>
      <div className='card'>
        <div className='card-header'>{translate('Import recipients')}</div>
        <div className='card-body'>
          <div className='badge badge-info block'>
            {translate('One email per line or one JSON per line.')}<br/>
            {translate('Examples:')}<br/>
            <br/>
            <div className='font-mono'>{JSON.stringify(example1)}</div>
            <div className='font-mono'>{JSON.stringify(example2)}</div>
          </div>
          <textarea
            className='w-full h-80 mt-2'
            ref={refEmails}
            placeholder={translate('One email per line or one JSON per line.')}
          ></textarea>
          <button
            className='btn btn-add-outline mt-2 w-full'
            onClick={() => {
              request.post(
                'email-marketing/api/import-recipients',
                {
                  idCampaign: form.id,
                  recipients: refEmails.current.value,
                },
                {},
                (data: any) => {
                  refTableRecipients.current.reload();
                }
              )
            }}
          >
            <span className='icon'><i className='fas fa-upload'></i></span>
            <span className='text'>{translate('Import recipients')}</span>
          </button>
        </div>
      </div>
      <div className='card'>
        <div className='card-body'>
          <button
            className='btn btn-danger'
            onClick={() => {
              if (confirm('Are you sure to delete all recipients in this email?')) {
                request.post(
                  'email-marketing/api/remove-all-recipients',
                  { idCampaign: form.id },
                  {},
                  (data: any) => {
                    refTableRecipients.current.reload();
                  }
                );
              }
            }}
          >
            <span className='icon'><i className='fas fa-trash'></i></span>
            <span className='text'>{translate('Remove all recipients')}</span>
          </button>
        </div>
      </div>
    </div>
  </div>;
}

/**
 * FormCampaign
 *
 * @var [type]
 */
const FormCampaign = (props: FormCampaignProps) => {
  let tabs: FormTabs = {
    default: { title: <b>{translate('Campaign')}</b>, content: () => <TabDefault {...props} /> },
  }

  if (props.id > 0) {
    tabs['calendar'] ={ title: translate('Recipients'), content: () => <TabRecipients /> };
  }

  return <Form
    componentName='FormTeam'
    parentApp='Hubleto/App/Community/EmailMarketing'
    model='Hubleto/App/Community/EmailMarketing/Models/Campaign'
    urlSlug='email-marketing/campaigns'
    endpointParams={{saveRelations: ['TAGS'] }}
    onAfterFormInitialized={(form: any) => {
      form.setReadonly(form.recordStore.getField('is_closed') == 1);
    }}
    {...props}
    
    uiComponents={{
      title: <Title {...props} />,
      tabs: tabs,
    }}
  ></Form>;
}

export default FormCampaign;