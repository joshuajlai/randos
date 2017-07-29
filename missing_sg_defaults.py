#!/usr/bin/env python

import argparse
import json
import os
import sys

class Detector(object):

    def __init__(self, filename):
        self.filename = filename

    def run(self):
        data = self.reindexData(self.loadFile())
        vpc_index = self.returnVPCsMissingDefault(data)
        return vpc_index

    def returnVPCsMissingDefault(self, data):
        vpc_index = {}
        for vpc_id, security_groups in data.items():
            has_default = False
            for security_group in security_groups:
                if 'GroupName' not in security_group:
                    continue
                if security_group['GroupName'].lower() == 'default':
                    has_default = True
                    break
            vpc_index[vpc_id] = has_default
        return vpc_index

    def reindexData(self, data):
        reindexed_data = {}
        for security_group in data:
            if 'VpcId' not in security_group:
                continue
            vpc_id = security_group['VpcId']
            if vpc_id not in reindexed_data:
                reindexed_data[vpc_id] = []
            reindexed_data[vpc_id].append(security_group)

        return reindexed_data

    def loadFile(self):
        if not os.path.isfile(self.filename):
            raise Exception("File not found: " + self.filename)
        input = open(self.filename, 'r')
        json_blob = json.load(input)
        if 'SecurityGroups' not in json_blob:
            raise Exception("Invalid json file provided")

        return json_blob['SecurityGroups']


def main():
    """Main entrypoint."""
    parser = argparse.ArgumentParser(
        description="Loads a specified json file and looks for vpcs without default security groups"
    )
    parser.add_argument('-f', '--file', type=str, required=True)
    args = parser.parse_args()

    detector = Detector(args.file)
    vpc_index = detector.run()

    for vpc_id, has_default in vpc_index.items():
        if not has_default:
            print(vpc_id)

if __name__ == "__main__":
    main()
