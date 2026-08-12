import React, { Component } from 'react'
import FormCampaignSchedule, { FormCampaignScheduleProps } from './FormCampaignSchedule';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';

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
    formUrlSlug='email-marketing/schedules'
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
          return <button
            key={key}
            className='btn btn-transparent btn-list-item'
            onClick={() => table.openForm(record.id)}
          >
            <div className='icon text-center bg-primary/20 rounded-sm h-full'>
              Day<br/>
              <b>{record.day}</b>
            </div>
            <div className='text'>
              {record.id_email > 0 ? <>
                <div className='text-gray-300'>
                  From: {record.EMAIL?.SENDER_ACCOUNT?.name ?? <span className='text-red-800'>n/a</span>}
                </div>
                <div className='text-gray-300'>
                  {record.EMAIL?.title ?? ''}
                </div>
                <div className='fond-bold'>
                  {record.EMAIL?.mail_subject ?? '-'}
                </div>
              </> : <div className='text-red-800'>No email selected</div>}
            </div>
          </button>;
        })}
      </div>
    }}
    {...props}
  ></Table>
}

export default TableCampaignsSchedules;
