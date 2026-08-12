import React, { Component } from 'react'
import FormCampaignScheduleRecipient, { FormCampaignScheduleRecipientProps } from './FormCampaignScheduleRecipient';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';

interface TableCampaignsSchedulesRecipientsProps extends TableProps {
  idCampaignSchedule?: number,
}

const componentName = 'TableCampaignsSchedulesRecipients';
const parentApp = 'Hubleto/App/Community/EmailMarketing';

const TableCampaignsSchedulesRecipients = (props: TableCampaignsSchedulesRecipientsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/CampaignScheduleRecipient'}
    endpointParams={{idCampaignSchedule: props.idCampaignSchedule}}
    formUrlSlug='email-marketing/schedules/recipients'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_campaign_schedule: props.idCampaignSchedule}}
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
      return <FormCampaignScheduleRecipient {...table.getDefaultFormProps()}/>;
    }}
  ></Table>
}

export default TableCampaignsSchedulesRecipients;
