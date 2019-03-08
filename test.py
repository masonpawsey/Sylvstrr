TEST = {
        "Test": {
            "Test1": "Test1.1"
        }
}

def read():
    return [TEST[i] for i in sorted(TEST.keys())]
