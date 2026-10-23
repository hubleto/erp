import React, { Component } from 'react'
import FormCampaignSchedule from './FormCampaignSchedule';
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import request from '@hubleto/react-ui/core/Request';
import Translator from '@hubleto/react-ui/core/Translator';

interface TableCampaignsSchedulesProps extends TableProps {
  idCampaign?: number,
}

const componentName = 'TableCampaignsSchedules';
const parentApp = 'Hubleto/App/Community/EmailMarketing';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableCampaignsSchedules = (props: TableCampaignsSchedulesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/CampaignSchedule'}
    endpointParams={{idCampaign: props.idCampaign}}
    baseUrlSlug='email-marketing/schedules'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_campaign: props.idCampaign}}
    getRowClassName={(table: TableMeta, rowData: any): string => {
      return rowData.is_closed ? 'bg-slate-300' : table.getDefaultRowClassName(rowData);
    }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "virt_tags") {
        return data.TAGS.map((tag, key) => {
          return <div key={key} className="text-nowrap mr-2">
            <i style={{color: tag.TAG?.color}} className="fas fa-tag mr-2"></i>
            {tag.TAG?.name}
          </div>;
        });
      } else return table.renderDefaultCell(columnName, column, data, options);
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormCampaignSchedule {...table.getDefaultFormProps()}/>;
    }}
    renderRecords={(table: TableMeta): React.JSX.Element => {
      return <div className='list mt-2'>
        {table.data?.records.map((record, key) => {
          const CAMPAIGN = record.CAMPAIGN;
          const numberOfRecipients = record.RECIPIENTS ? record.RECIPIENTS.length : 0;
          const sendToCount = CAMPAIGN?.RECIPIENTS?.length - numberOfRecipients;

          return <div className='flex gap-2'>
            <button
              key={key}
              className='btn btn-transparent btn-list-item'
              onClick={() => table.openForm(record.id)}
            >
              <div className='icon text-center bg-primary/20 rounded-sm h-full'>
                {T.translate('Day')}<br/>
                <b>{record.day}</b>
              </div>
              <div className={'text block ' + (record.EMAIL?.is_closed ? 'striped-45': '')}>
                {record.id_email > 0 ? <>
                  <div className='fond-bold'>
                    {record.EMAIL?.mail_subject ?? '-'}
                  </div>
                  <div className={'badge ' + (record.EMAIL?.SENDER_ACCOUNT?.name ? '' : 'badge-danger')}>
                    {record.EMAIL?.SENDER_ACCOUNT?.name ?? <>{T.translate('No sender account')}</>}
                  </div>
                  {record.EMAIL?.is_approved ? <div className='badge badge-success'>{T.translate('Approved')}</div> : <div className='badge badge-danger'>{T.translate('Not approved')}</div>}
                  <div className='badge badge-info'>{numberOfRecipients} {T.translate('recipients')}</div>
                  {record.EMAIL?.is_closed ? <div className='badge badge-danger'>{T.translate('Closed')}</div> : null}
                </> : <div className='text-red-800'>{T.translate('No email selected')}</div>}
              </div>
            </button>
            <div className='m-2'>
              {CAMPAIGN?.type == 2 /* newsletter */
               && record.EMAIL?.id_sender_account > 0
               && record.EMAIL?.is_approved
               && !record.EMAIL?.is_closed ? <div className='btn-group'>
                <button
                  className={'btn ' + (sendToCount <= 0 ? 'btn-transparent' : 'btn-warning')}
                  onClick={() => {
                    request.post(
                      'email-marketing/api/launch-email-in-campaign',
                      { idCampaign: props.idCampaign, idEmail: record.id_email },
                      {},
                      (data: any) => {
                        table.props?.parentForm?.reload();
                      }
                    )
                  }}
                >
                  <span className='icon'><i className='fas fa-bolt'></i></span>
                  <span className='text'>{T.translate('Send to')} {sendToCount}</span>
                </button>
                <button
                  className='btn btn-transparent'
                  onClick={() => {
                    request.post(
                      'email-marketing/api/close-email-in-campaign',
                      { idCampaign: props.idCampaign, idEmail: record.id_email },
                      {},
                      (data: any) => {
                        table.props?.parentForm?.reload();
                      }
                    )
                  }}
                >
                  <span className='icon'><i className='fas fa-times'></i></span>
                  <span className='text'>{T.translate('Close')}</span>
                </button>
              </div> : null}
            </div>
          </div>;
        })}
      </div>
    }}
    {...props}
  ></Table>
}

export default TableCampaignsSchedules;
