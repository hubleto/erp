import React, { Component } from 'react';
import HubletoForm, { HubletoFormProps, HubletoFormState } from '@hubleto/react-ui/ext/HubletoForm';
import TableContacts from '@hubleto/apps/Contacts/Components/TableContacts';
import TableTasks from '@hubleto/apps/Tasks/Components/TableTasks';
import PipelineSelector from '../../Pipeline/Components/PipelineSelector';
import request from '@hubleto/react-ui/core/Request';

export interface FormCampaignProps extends HubletoFormProps {}
export interface FormCampaignState extends HubletoFormState {
  mailPreviewInfo?: any,
}

export default class FormCampaign<P, S> extends HubletoForm<FormCampaignProps, FormCampaignState> {
  static defaultProps: any = {
    ...HubletoForm.defaultProps,
    model: 'Hubleto/App/Community/Campaigns/Models/Campaign',
  };

  props: FormCampaignProps;
  state: FormCampaignState;

  translationContext: string = 'Hubleto\\App\\Community\\Campaigns\\Loader::Components\\FormCampaign';

  parentApp: string = 'Hubleto/App/Community/Campaigns';

  constructor(props: FormCampaignProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormCampaignProps) {
    return {
      ...super.getStateFromProps(props),
      tabs: [
        { uid: 'default', title: <b>{this.translate('Campaign')}</b> },
        { uid: 'contacts', title: this.translate('Contacts'), showCountFor: 'CONTACTS' },
        { uid: 'tasks', title: this.translate('Tasks'), showCountFor: 'TASKS' },
        ...(this.getParentApp()?.getFormTabs() ?? [])
      ]
    };
  }

  getRecordFormUrl(): string {
    return 'campaigns/' + this.state.record.id;
  }

  contentClassName(): string
  {
    return this.state.record.is_closed ? 'opacity-85 bg-slate-100' : '';
  }

  renderTitle(): JSX.Element {
    return <>
      <small>{this.translate("Campaign")}</small>
      <h2>{this.state.record.name ?? '-'}</h2>
    </>;
  }

  renderTopMenu(): JSX.Element {
    const R = this.state.record;
    return <>
      {super.renderTopMenu()}
      {this.state.id <= 0 ? null : <>
        <PipelineSelector
          idPipeline={R.id_pipeline}
          idPipelineStep={R.id_pipeline_step}
          onPipelineChange={(idPipeline: number, idPipelineStep: number) => {
            this.updateRecord({id_pipeline: idPipeline, id_pipeline_step: idPipelineStep});
          }}
          onPipelineStepChange={(idPipelineStep: number, step: any) => {
            this.updateRecord({id_pipeline_step: idPipelineStep});
          }}
        ></PipelineSelector>
        {this.inputWrapper('is_closed')}
      </>}
    </>
  }

  renderTab(tabUid: string) {
    const R = this.state.record;

    switch (tabUid) {
      case 'default':
        return <>
          <div className='w-full flex gap-2'>
            <div className='flex-1 border-r border-gray-100'>
              {this.inputWrapper('name')}
              {this.inputWrapper('target_audience')}
              {this.inputWrapper('goal')}
              {this.inputWrapper('id_mail_template')}
              {this.inputWrapper('mail_body')}
            </div>
            <div className='flex-1'>
              {this.inputWrapper('utm_source')}
              {this.inputWrapper('utm_campaign')}
              {this.inputWrapper('utm_term')}
              {this.inputWrapper('utm_content')}
              {this.inputWrapper('id_manager')}
              {this.inputWrapper('notes')}
              {this.inputWrapper('color')}
              {this.inputWrapper('datetime_created')}
              {this.inputWrapper('uid')}
            </div>
          </div>
        </>;
      break

      case 'contacts':
        const mailPreviewInfo: any = this.state.mailPreviewInfo;
        return <div className='flex flex-col gap-2'>
          <div className='overflow-y-auto'>
            <TableContacts
              tag={"table_campaign_contact"}
              parentForm={this}
              uid={this.props.uid + "_table_campaign_contact"}
              selectionMode='multiple'
              readonly={true}
              selection={R.CONTACTS.map((item) => { return { id: item.id_contact } })}
              onSelectionChange={(table: any) => {
                request.post(
                  'campaigns/api/save-contacts',
                  {
                    idCampaign: this.state.record.id,
                    contactIds: table.state.selection.map((item) => item.id)
                  },
                  {},
                  (result: any) => {
                    console.log(result);
                  }
                );
              }}
              onRowClick={(table: any, row: any) => {
                console.log('onRowClick', row);
                request.post(
                  'campaigns/api/get-mail-preview-info',
                  {
                    idCampaign: this.state.record.id,
                    idContact: row.id,
                  },
                  {},
                  (result: any) => {
                    console.log(result);
                    this.setState({mailPreviewInfo: result});
                  }
                );
              }}
              // junctionTitle='Campaign'
              // junctionModel='Hubleto/App/Community/Campaigns/Models/CampaignContact'
              // junctionSourceColumn='id_campaign'
              // junctionSourceRecordId={R.id}
              // junctionDestinationColumn='id_contact'
            />
          </div>
          {mailPreviewInfo && mailPreviewInfo.CONTACT && mailPreviewInfo.bodyHtml != '' ? 
            <div className=''>
              <div className='card'>
                <div className='card-header'>
                  Mail preview
                  <button
                    className='btn btn-transparent'
                    onClick={() => { this.setState({mailPreviewInfo: null}); }}
                  ><span className='icon'><i className='fas fa-times'></i></span></button>
                </div>
                <div className='card-body'>
                  <div><b>Contact</b></div>
                  <div className='text-sm bg-slate-100 p-2 flex gap-2'>
                    <div className='font-bold'>{mailPreviewInfo.CONTACT?.first_name}</div>
                    <div className='font-bold'>{mailPreviewInfo.CONTACT?.last_name}</div>
                    {mailPreviewInfo.CONTACT?.VALUES?.map((item, key) => {
                      return <div key={key}>{item.value}</div>;
                    })}
                  </div>
                  <div><b>Preview</b></div>
                  <div
                    className='text-blue-800 max-h-72'
                    dangerouslySetInnerHTML={{__html: mailPreviewInfo.bodyHtml}}
                  ></div>
                </div>
              </div>
            </div>
          : null}
        </div>
      break;

      case 'tasks':
        return <TableTasks
          tag={"table_campaign_task"}
          parentForm={this}
          uid={this.props.uid + "_table_campaign_task"}
          junctionTitle='Campaign'
          junctionModel='Hubleto/App/Community/Campaigns/Models/CampaignTask'
          junctionSourceColumn='id_campaign'
          junctionSourceRecordId={R.id}
          junctionDestinationColumn='id_task'
        />;
      break;

      default:
        return super.renderTab(tabUid);
      break;
    }
  }
}

