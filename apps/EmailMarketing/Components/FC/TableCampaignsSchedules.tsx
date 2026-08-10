import React, { Component } from 'react'
import FormCampaignSchedule, { FormCampaignScheduleProps } from './FormCampaignSchedule';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';

interface TableCampaignsSchedulesProps extends TableProps {
  idCampaign?: number,
}

const TableCampaignsSchedules = (props: TableCampaignsSchedulesProps) => {
  return <Table
    componentName='TableCampaignsSchedules'
    model='Hubleto/App/Community/EmailMarketing/Models/CampaignSchedule'
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
    {...props}
  ></Table>
}

export default TableCampaignsSchedules;
