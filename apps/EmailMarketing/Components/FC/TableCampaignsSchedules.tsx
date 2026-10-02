import React, { Component } from 'react'
import FormCampaignSchedule from './FormCampaignSchedule';
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import request from '@hubleto/react-ui/core/Request';

interface TableCampaignsSchedulesProps extends TableProps {
  idCampaign?: number,
}

const componentName = 'TableCampaignsSchedules';
const parentApp = 'Hubleto/App/Community/EmailMarketing';

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
          return <div className='flex gap-2'>
            <button
              key={key}
              className='btn btn-transparent btn-list-item'
              onClick={() => table.openForm(record.id)}
            >
              <div className='icon text-center bg-primary/20 rounded-sm h-full'>
                Day<br/>
                <b>{record.day}</b>
              </div>
              <div className={'text block ' + (record.EMAIL?.is_closed ? 'striped-45': '')}>
                {record.id_email > 0 ? <>
                  <div className='fond-bold'>
                    {record.EMAIL?.mail_subject ?? '-'}
                  </div>
                  <div className={'badge ' + (record.EMAIL?.SENDER_ACCOUNT?.name ? '' : 'badge-danger')}>
                    {record.EMAIL?.SENDER_ACCOUNT?.name ?? <>No sender account</>}
                  </div>
                  {record.EMAIL?.is_approved ? <div className='badge badge-success'>Approved</div> : <div className='badge badge-danger'>Not approved</div>}
                  <div className='badge badge-info'>{record.RECIPIENTS ? record.RECIPIENTS.length : 0} recipients</div>
                  {record.EMAIL?.is_closed ? <div className='badge badge-danger'>Closed</div> : null}
                </> : <div className='text-red-800'>No email selected</div>}
              </div>
            </button>
            <div className='m-2'>
              {record.EMAIL?.is_approved && !record.EMAIL?.is_closed ? 
                <button
                  className='btn btn-transparent'
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
                  <span className='text'>Launch</span>
                </button>
              : null}
            </div>
          </div>;
        })}
      </div>
    }}
    {...props}
  ></Table>
}

export default TableCampaignsSchedules;
